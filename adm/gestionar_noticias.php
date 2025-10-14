<?php
session_start();
if(!isset($_SESSION['login'])){
    header("location:login.php");
    exit();
} else {
    // Control de inactividad
    $fecGuar = $_SESSION["hora"];
    $ahora = date("Y-n-j H:i:s");
    $tmpTrans = (strtotime($ahora)-strtotime($fecGuar));
    if($tmpTrans >= 12000){ // 200 minutos
        session_destroy();
        header("Location: login.php?mensaje=Sesión+expirada");
        exit();
    } else {
        $_SESSION["hora"] = $ahora;
    }
}

$nivel_usuario = $_SESSION['nivel'];

// Conexión a la DB para obtener las noticias existentes
require_once("script/conex_mysqli.php");
$cn = new MySQLcn();
$cn->query("SELECT idNoticia, titulo, imagen, estado FROM noticias ORDER BY fecha DESC");
$noticias_existentes = $cn->fetchAll();
$cn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Noticias - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Segoe+UI&display=swap" rel="stylesheet">
    <style>
        :root { --primary-blue: #1e3a5f; --accent-orange: #ff8c42; --light-gray: #f5f7fa; --font-body: 'Segoe UI', sans-serif; --font-script: 'Dancing Script', cursive; }
        body { display: flex; min-height: 100vh; font-family: var(--font-body); background-color: var(--light-gray); }
        .sidebar { width: 260px; background-color: var(--primary-blue); color: white; position: fixed; height: 100%; padding-top: 1.5rem; }
        .sidebar .logo { font-family: var(--font-script); font-size: 2.5rem; text-align: center; margin-bottom: 2rem; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); font-size: 1.05rem; padding: 0.8rem 1.5rem; display: flex; align-items: center; }
        .sidebar .nav-link .fa-fw { width: 1.5em; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: var(--accent-orange); color: white; }
        .sidebar .user-info { padding: 1.5rem; text-align: center; border-top: 1px solid rgba(255,255,255,0.2); position: absolute; bottom: 0; width: 100%; }
        .main-content { margin-left: 260px; flex-grow: 1; padding: 2rem; }
        .main-content .page-title { font-family: var(--font-script); font-size: 3.5rem; color: var(--primary-blue); }
        .main-content .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .upload-area { border: 2px dashed #ccc; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer; }
        .preview-image { max-width: 100%; max-height: 250px; margin-top: 15px; border-radius: 8px; }
        .btn-submit { background: var(--accent-orange); color: white; border: none; border-radius: 8px; font-weight: 600; transition: background 0.3s; }
        .btn-submit:hover { background: #e67e22; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h1 class="logo">QA Soft</h1>
        <nav class="nav flex-column">
            <a class="nav-link" href="user.php"><i class="fas fa-tachometer-alt fa-fw"></i> Dashboard</a>
            <a class="nav-link" href="gestionar_banners.php"><i class="fas fa-images fa-fw"></i> Banners</a>
            <a class="nav-link active" href="gestionar_noticias.php"><i class="far fa-newspaper fa-fw"></i> Noticias</a>
            <?php if ($nivel_usuario == 1): ?>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="#"><i class="fas fa-users-cog fa-fw"></i> Usuarios</a>
            <?php endif; ?>
        </nav>
        <div class="user-info">
            <strong><?php echo $_SESSION["nombre"]; ?></strong><br>
            <a href="logout.php" class="btn btn-sm btn-warning mt-2">Cerrar Sesión</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title">Gestión de Noticias</h2>
            <a href="../noticias.php" target="_blank" class="btn btn-outline-secondary">Ver Página de Noticias <i class="fas fa-external-link-alt ms-2"></i></a>
        </header>

        <div class="container-fluid">
            <div class="row">
                <!-- Formulario para añadir noticia -->
                <div class="col-lg-5">
                    <div class="card p-4">
                        <h4 class="mb-4">Añadir Nueva Noticia</h4>
                        <form action="procesar_noticia.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3"><label for="titulo" class="form-label">Título</label><input type="text" class="form-control" id="titulo" name="titulo" required></div>
                            <div class="mb-3"><label for="nota" class="form-label">Nota o Contenido</label><textarea class="form-control" id="nota" name="nota" rows="4" required></textarea></div>
                            <div class="mb-3"><label for="imagen" class="form-label">Imagen</label><div class="upload-area" id="uploadArea"><p class="mb-0"><i class="fas fa-cloud-upload-alt fa-2x mb-2"></i><br>Arrastra o haz clic</p><input type="file" class="d-none" id="imagen" name="imagen" accept="image/*" required></div></div>
                            <div id="preview" class="text-center mb-3 border rounded p-2">Vista Previa</div>
                            <div class="form-check form-switch mb-4"><input class="form-check-input" type="checkbox" id="estado" name="estado" checked><label class="form-check-label" for="estado">Publicar al subir</label></div>
                            <button type="submit" class="btn-submit w-100 p-2">Guardar Noticia</button>
                        </form>
                    </div>
                </div>

                <!-- Tabla de noticias existentes -->
                <div class="col-lg-7">
                    <div class="card p-4">
                        <h4 class="mb-4">Noticias Actuales</h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead><tr><th>Imagen</th><th>Título</th><th>Estado</th><th>Acciones</th></tr></thead>
                                <tbody>
                                    <?php foreach($noticias_existentes as $noticia): ?>
                                    <tr>
                                        <td><img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" width="100" class="rounded"></td>
                                        <td><?php echo htmlspecialchars($noticia['titulo']); ?></td>
                                        <td><span class="badge bg-<?php echo $noticia['estado'] ? 'success' : 'secondary'; ?>"><?php echo $noticia['estado'] ? 'Publicada' : 'Borrador'; ?></span></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="#" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($noticias_existentes)): ?>
                                    <tr><td colspan="4" class="text-center">No hay noticias para mostrar.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('imagen');
        const preview = document.getElementById('preview');
        uploadArea.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', e => showPreview(e.target.files[0]));
        ['dragover', 'drop'].forEach(eName => uploadArea.addEventListener(eName, e => e.preventDefault()));
        uploadArea.addEventListener('drop', e => { fileInput.files = e.dataTransfer.files; showPreview(fileInput.files[0]); });
        function showPreview(file) {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => { preview.innerHTML = `<img src="${e.target.result}" class="preview-image img-fluid">`; };
                reader.readAsDataURL(file);
            } else { preview.innerHTML = `Vista Previa`; }
        }
    </script>
</body>
</html>
