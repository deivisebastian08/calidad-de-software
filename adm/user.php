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

// Definir el nivel de usuario y la sección actual
$user_level = isset($_SESSION['nivel']) ? $_SESSION['nivel'] : 'Invitado';
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'dashboard'; // Sección por defecto

// Proteger secciones solo para administradores
if ($seccion == 'usuarios' && $user_level != 'Administrador') {
    $seccion = 'dashboard'; // Redirigir a dashboard si no es admin
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - QA Software</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        body { font-family: var(--font-body); background: var(--light-gray); color: var(--text-dark); }
        .container-fluid { max-width: 1600px; }

        /* Layout del Dashboard */
        .wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: var(--primary-blue); color: white; position: fixed; height: 100%; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 2rem 3rem; }

        /* Sidebar */
        .sidebar-header { padding: 1.5rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header .logo { font-family: var(--font-script); font-size: 2.5rem; color: white; text-decoration: none; }
        .admin-menu { list-style: none; padding: 0; margin: 1rem 0; }
        .admin-menu a { display: block; padding: 1rem 1.5rem; color: rgba(255,255,255,0.8); text-decoration: none; font-size: 1rem; transition: all 0.3s; border-left: 4px solid transparent; }
        .admin-menu a:hover { background: rgba(255,255,255,0.05); color: white; border-left-color: var(--accent-orange); }
        .admin-menu a.active { background: rgba(0,0,0,0.2); color: white; border-left-color: var(--accent-orange); font-weight: 600; }
        .admin-menu i { margin-right: 12px; width: 20px; text-align: center; }
        .user-info { padding: 1.5rem; background: rgba(0,0,0,0.2); text-align: center; position: absolute; bottom: 0; width: 100%; }
        .user-info h5 { margin: 0; font-size: 1.1rem; } .user-info p { margin: 0; font-size: 0.85rem; opacity: 0.7; }
        .user-info .btn-logout { background: var(--accent-orange); border: none; color: white; margin-top: 1rem; }

        /* Contenido Principal */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-family: var(--font-script); font-size: 3.5rem; color: var(--primary-blue); }
        .card-admin { background: white; padding: 2.5rem; border-radius: 12px; border-top: 4px solid var(--accent-orange); box-shadow: 0 8px 30px rgba(0,0,0,0.07); }
    </style>
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="../index.php" class="logo">QA Software</a>
            </div>
            <ul class="admin-menu">
                <li><a href="user.php?seccion=dashboard" class="<?php echo $seccion == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                <li><a href="user.php?seccion=banners" class="<?php echo $seccion == 'banners' ? 'active' : ''; ?>"><i class="fas fa-images"></i>Gestionar Banners</a></li>
                <li><a href="user.php?seccion=noticias" class="<?php echo $seccion == 'noticias' ? 'active' : ''; ?>"><i class="fas fa-newspaper"></i>Gestionar Noticias</a></li>
                <?php if ($user_level == 'Administrador'): ?>
                <li><a href="user.php?seccion=usuarios" class="<?php echo $seccion == 'usuarios' ? 'active' : ''; ?>"><i class="fas fa-users-cog"></i>Gestionar Usuarios</a></li>
                <?php endif; ?>
            </ul>
            <div class="user-info">
                <h5><?php echo $_SESSION["nombre"]; ?></h5>
                <p><?php echo $_SESSION["nivel"]; ?></p>
                <a href="logout.php" class="btn btn-sm btn-logout">Cerrar Sesión</a>
            </div>
        </aside>

        <main class="main-content">
            <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if ($seccion == 'dashboard'): ?>
                <h1 class="page-title">Bienvenido, <?php echo explode(' ', $_SESSION["nombre"])[0]; ?></h1>
                <p class="lead text-muted">Este es tu panel de administración. Desde aquí puedes gestionar el contenido del sitio web.</p>
                <!-- Aquí se podrían agregar estadísticas o accesos directos -->
            <?php endif; ?>

            <?php if ($seccion == 'banners'): ?>
                <h1 class="page-title">Gestionar Banners</h1>
                <div class="card-admin">
                    <h4 class="mb-4">Subir Nuevo Banner</h4>
                    <form action="procesar_imagen.php" method="POST" enctype="multipart/form-data">
                        <!-- El formulario de banners que ya existía -->
                        <div class="mb-3"><label for="titulo" class="form-label">Título del Banner</label><input type="text" class="form-control" id="titulo" name="titulo" required></div>
                        <div class="mb-3"><label for="descripcion" class="form-label">Descripción</label><textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea></div>
                        <div class="mb-3"><label for="link" class="form-label">Enlace (URL)</label><input type="text" class="form-control" id="link" name="link"></div>
                        <div class="mb-3"><label for="imagen" class="form-label">Imagen del Banner</label><input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required></div>
                        <div class="form-check form-switch mb-4"><input class="form-check-input" type="checkbox" id="estado" name="estado" checked><label class="form-check-label" for="estado">Activar este banner al subir</label></div>
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-upload me-2"></i>Subir Banner</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($seccion == 'noticias'): ?>
                <h1 class="page-title">Gestionar Noticias</h1>
                <div class="card-admin">
                    <p>Aquí irá el formulario y la tabla para administrar las noticias (crear, editar, eliminar).</p>
                </div>
            <?php endif; ?>

            <?php if ($seccion == 'usuarios' && $user_level == 'Administrador'): ?>
                <h1 class="page-title">Gestionar Usuarios</h1>
                <div class="card-admin">
                    <p>Aquí irá la tabla para administrar los usuarios y sus niveles de acceso (crear, editar, eliminar).</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
