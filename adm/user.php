<?php
session_start();
if(!isset($_SESSION['login'])){
    header("location:login.php");
    exit();
} else {
    $fecGuar = $_SESSION["hora"];
    $ahora = date("Y-n-j H:i:s");
    $tmpTrans = (strtotime($ahora)-strtotime($fecGuar));

    if($tmpTrans >= 12000){ // 200 minutos de inactividad
        session_destroy();
        header("Location: login.php?mensaje=Sesión+expirada+por+inactividad");
        exit();
    } else {
        $_SESSION["hora"] = $ahora;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - QA Software</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts para la caligrafía -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Segoe+UI&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1e3a5f;
            --accent-orange: #ff8c42;
            --light-gray: #f5f7fa;
            --text-dark: #1a202c;
            --text-gray: #4a5568;
            --font-body: 'Segoe UI', 'Arial', sans-serif;
            --font-script: 'Dancing Script', cursive;
        }
        body { font-family: var(--font-body); background: var(--light-gray); color: var(--text-dark); line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* Navegación */
        .navbar-custom { background: var(--primary-blue); padding: 1rem 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-custom .container { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-family: var(--font-script); font-size: 2.2rem; color: white; text-decoration: none; text-transform: capitalize; }
        .nav-menu { display: flex; list-style: none; gap: 1.5rem; margin-bottom: 0; align-items: center; }
        .nav-menu a { color: rgba(255,255,255,0.9); text-decoration: none; font-size: 0.95rem; font-weight: 500; }
        .nav-menu a:hover { color: var(--accent-orange); }
        .user-info { color: white; font-weight: 500; }

        /* Contenido Principal */
        .main-content { padding: 80px 0; }
        .admin-title { font-family: var(--font-script); font-size: 3.8rem; text-align: center; margin-bottom: 2rem; color: var(--primary-blue); }
        
        /* Tarjeta de subida */
        .upload-card { background: white; max-width: 800px; margin: 0 auto; padding: 2.5rem; border-radius: 12px; border-top: 4px solid var(--accent-orange); box-shadow: 0 8px 30px rgba(0,0,0,0.1); }
        .form-label { font-weight: 600; color: var(--text-dark); }
        .form-control, .form-select { border-radius: 8px; padding: 0.75rem 1rem; }
        .form-control:focus { border-color: var(--accent-orange); box-shadow: 0 0 0 3px rgba(255, 140, 66, 0.2); }
        .upload-area { border: 2px dashed #ccc; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer; transition: border-color 0.3s; }
        .upload-area:hover { border-color: var(--accent-orange); }
        .preview-image { max-width: 100%; max-height: 250px; margin-top: 15px; border-radius: 8px; }
        .btn-submit { background: var(--accent-orange); color: white; width: 100%; padding: 0.9rem; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background 0.3s; }
        .btn-submit:hover { background: #e67e22; }

        /* Footer */
        .footer { background: var(--primary-blue); padding: 2.5rem 0; text-align: center; margin-top: 5rem; }
        .footer p { color: rgba(255,255,255,0.9); font-size: 0.95rem; margin: 0; }
    </style>
</head>
<body>
    <nav class="navbar-custom">
        <div class="container">
            <a href="../index.php" class="logo">QA Software</a>
            <ul class="nav-menu">
                <li class="user-info"><i class="fas fa-user-circle me-2"></i><?php echo $_SESSION["nombre"]; ?></li>
                <li><a href="../index.php" class="btn btn-sm btn-outline-light">Ver Sitio</a></li>
                <li><a href="logout.php" class="btn btn-sm btn-warning">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <h1 class="admin-title">Panel de Administración</h1>

            <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="max-width: 800px; margin: 0 auto 2rem auto;">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($_GET['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="upload-card">
                <h4 class="mb-4">Subir Nuevo Banner</h4>
                <form action="procesar_imagen.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título del Banner</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="link" class="form-label">Enlace (URL)</label>
                        <input type="text" class="form-control" id="link" name="link">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="imagen" class="form-label">Imagen del Banner</label>
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                <p class="mb-0">Arrastra o haz clic para subir</p>
                                <input type="file" class="d-none" id="imagen" name="imagen" accept="image/*" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vista Previa</label>
                            <div id="preview" class="text-center border rounded p-2 h-100 d-flex align-items-center justify-content-center">Sin imagen</div>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="estado" name="estado" checked>
                        <label class="form-check-label" for="estado">Activar este banner al subir</label>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-upload me-2"></i>Subir Banner</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Calidad de Software. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('imagen');
        const preview = document.getElementById('preview');

        uploadArea.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', e => showPreview(e.target.files[0]));

        ['dragover', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, e => e.preventDefault());
        });

        uploadArea.addEventListener('drop', e => {
            fileInput.files = e.dataTransfer.files;
            showPreview(fileInput.files[0]);
        });

        function showPreview(file) {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.innerHTML = `<img src="${e.target.result}" class="preview-image img-fluid" alt="Vista previa">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `Sin imagen`;
            }
        }
    </script>
</body>
</html>
