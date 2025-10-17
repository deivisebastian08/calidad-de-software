document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.getElementById('mainContent');
    const navLinks = document.querySelectorAll('.nav-link');
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const modalContainer = document.getElementById('modal-container');

    let currentPage = 1;
    let currentSearch = '';
    let currentEntity = 'dashboard';

    // --- UTILITIES ---
    const api = async (endpoint, options = {}) => {
        try {
            const response = await fetch(`api.php${endpoint}`, options);
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            showToast('Error de Conexión', error.message, 'danger');
            return { status: 'error', message: error.message };
        }
    };

    const showToast = (title, message, type = 'success') => {
        const toastContainer = document.querySelector('.toast-container');
        const toastId = `toast-${Date.now()}`;
        const toastHTML = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>`;
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        const toast = new bootstrap.Toast(document.getElementById(toastId), { delay: 3000 });
        toast.show();
    };

    const renderPagination = (pagination) => {
        if (pagination.totalPages <= 1) return '';
        let html = '<nav class="mt-4"><ul class="pagination justify-content-center">';
        for (let i = 1; i <= pagination.totalPages; i++) {
            html += `<li class="page-item ${i === pagination.page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
        return html + '</ul></nav>';
    };

    // --- RENDER FUNCTIONS ---
    const renderDashboard = async () => {
        const stats = await api('?entity=stats');
        mainContent.innerHTML = `
            <h1 class="page-title">Bienvenido</h1>
            <p class="lead text-muted mb-4">Selecciona una opción para empezar a gestionar el contenido.</p>
            <div class="row g-4" id="dashboard-cards"></div>`;
        if (stats.status === 'success') {
            document.getElementById('dashboard-cards').innerHTML = `
                <div class="col-md-4"><a href="#news" class="nav-link quick-access-card text-center"><div class="icon"><i class="fas fa-newspaper"></i></div><span class="stat">${stats.data.news}</span><h4>Noticias</h4></a></div>
                <div class="col-md-4"><a href="#banners" class="nav-link quick-access-card text-center"><div class="icon"><i class="fas fa-images"></i></div><span class="stat">${stats.data.banners}</span><h4>Banners</h4></a></div>
                <div class="col-md-4"><a href="#users" class="nav-link quick-access-card text-center"><div class="icon"><i class="fas fa-users-cog"></i></div><span class="stat">${stats.data.users}</span><h4>Usuarios</h4></a></div>`;
        }
    };

    const renderNews = async () => {
        const result = await api(`?entity=news&search=${currentSearch}&page=${currentPage}`);
        let content = '<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="page-title m-0">Noticias</h1><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#news-modal-create"><i class="fas fa-plus-circle me-2"></i>Crear Noticia</button></div>';
        if (result.data.items.length > 0) {
            content += '<div class="row g-4">' + result.data.items.map(item => `
                <div class="col-md-6 col-lg-4">
                    <div class="card card-item h-100">
                        <img src="${item.imagen.startsWith('http') ? item.imagen : '../' + item.imagen}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${item.titulo}</h5>
                            <p class="card-text small text-muted flex-grow-1">${item.nota.substring(0, 100)}...</p>
                            <div class="text-end"><button class="btn btn-sm btn-outline-primary edit-btn" data-entity="news" data-id="${item.idNoticia}">Editar</button> <button class="btn btn-sm btn-outline-danger delete-btn" data-entity="news" data-id="${item.idNoticia}">Eliminar</button></div>
                        </div>
                    </div>
                </div>`).join('') + '</div>';
        } else {
            content += '<div class="card card-body text-center">No se encontraron noticias.</div>';
        }
        mainContent.innerHTML = content + renderPagination(result.data.pagination);
        renderModal('news'); // Render create modal
    };
    
    // --- MODAL & FORM HANDLING ---
    const renderModal = (entity, item = {}) => {
        const isUpdate = !!item.id;
        const modalId = `${entity}-modal-${isUpdate ? item.id : 'create'}`;
        let formFields = '';
        
        if (entity === 'news') {
            formFields = `
                <div class="mb-3"><label class="form-label">Título</label><input type="text" class="form-control" name="titulo" value="${item.titulo || ''}" required></div>
                <div class="mb-3"><label class="form-label">Contenido</label><textarea class="form-control" name="nota" rows="5" required>${item.nota || ''}</textarea></div>
                <div class="mb-3"><label class="form-label">Imagen (Subir o pegar URL)</label><input type="file" class="form-control mb-2" name="imagen" accept="image/*"><input type="url" class="form-control" name="imagen_url" value="${item.imagen || ''}" placeholder="https://ejemplo.com/imagen.jpg"></div>
            `;
        } // Add other entities like users/banners here

        const modalHTML = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form class="entity-form" data-entity="${entity}" data-id="${item.id || ''}">
                            <input type="hidden" name="action" value="${isUpdate ? 'update' : 'create'}">
                            <input type="hidden" name="id" value="${item.id || ''}">
                            <div class="modal-header"><h5 class="modal-title">${isUpdate ? 'Editar' : 'Crear'} ${entity.slice(0, -1)}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">${formFields}</div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Guardar</button></div>
                        </form>
                    </div>
                </div>
            </div>`;
        modalContainer.insertAdjacentHTML('beforeend', modalHTML);
        const modalElement = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalElement);
        if (isUpdate) modal.show();
        modalElement.addEventListener('hidden.bs.modal', () => modalElement.remove());
    };

    // --- ROUTING & NAVIGATION ---
    const navigate = async (hash) => {
        const entity = hash.substring(1) || 'dashboard';
        currentEntity = entity;
        currentPage = 1;
        currentSearch = '';
        searchInput.value = '';
        searchForm.style.visibility = (entity === 'dashboard') ? 'hidden' : 'visible';

        navLinks.forEach(l => l.classList.remove('active'));
        document.querySelector(`.nav-link[href="${hash || '#dashboard'}"]`)?.classList.add('active');

        mainContent.innerHTML = '<div class="d-flex justify-content-center p-5"><div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

        switch (entity) {
            case 'dashboard': await renderDashboard(); break;
            case 'news': await renderNews(); break;
            // Add cases for users and banners here
            default: mainContent.innerHTML = `<h1 class="page-title">Página no encontrada</h1>`;
        }
    };

    // --- EVENT LISTENERS ---
    window.addEventListener('hashchange', () => navigate(location.hash));
    navLinks.forEach(link => link.addEventListener('click', (e) => {
        e.preventDefault();
        const hash = new URL(link.href).hash;
        if (location.hash !== hash) location.hash = hash;
    }));

    let debounceTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentSearch = searchInput.value;
            currentPage = 1;
            navigate(`#${currentEntity}`);
        }, 350);
    });

    mainContent.addEventListener('click', async (e) => {
        if (e.target.matches('.page-link')) {
            e.preventDefault();
            currentPage = parseInt(e.target.dataset.page);
            await navigate(`#${currentEntity}`);
        }
        if (e.target.matches('.delete-btn')) {
            if (confirm('¿Estás seguro de que quieres eliminar este elemento?')) {
                const result = await api(`?entity=${e.target.dataset.entity}&action=delete&id=${e.target.dataset.id}`, { method: 'GET' });
                if (result.status === 'success') {
                    showToast('Eliminado', result.message);
                    await navigate(`#${currentEntity}`);
                }
            }
        }
        if (e.target.matches('.edit-btn')) {
            const entity = e.target.dataset.entity;
            const id = e.target.dataset.id;
            // This is a simplified way to get item data. In a real app, you might fetch it again.
            const itemData = { id, entity, titulo: 'Test', nota: 'Test' }; // Placeholder
            renderModal(entity, itemData);
        }
    });

    modalContainer.addEventListener('submit', async (e) => {
        if (e.target.matches('.entity-form')) {
            e.preventDefault();
            const form = e.target;
            const entity = form.dataset.entity;
            const formData = new FormData(form);
            const result = await api(`?entity=${entity}`, { method: 'POST', body: formData });
            if (result.status === 'success') {
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                modal.hide();
                showToast('Guardado', result.message);
                await navigate(`#${entity}`);
            }
        }
    });

    // Initial Load
    navigate(location.hash || '#dashboard');
});
