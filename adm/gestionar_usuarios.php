<?php
session_start();
// 1. VERIFICACIÓN DE ACCESO
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

// 2. SOLO ADMINS PUEDEN ACCEDER
if($nivel_usuario != 1) {
    header("location:user.php?mensaje=Acceso+denegado");
    exit();
}

// 3. OBTENER USUARIOS EXISTENTES
require_once("script/conex_mysqli.php");
$cn = new MySQLcn();
$cn->query("SELECT usersId, nombres, users, nivel, estado FROM usuarios ORDER BY usersId ASC");
$usuarios_existentes = $cn->fetchAll();
$cn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Admin</title>
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
            <a class="nav-link" href="gestionar_noticias.php"><i class="far fa-newspaper fa-fw"></i> Noticias</a>
            <?php if ($nivel_usuario == 1): ?>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link active" href="gestionar_usuarios.php"><i class="fas fa-users-cog fa-fw"></i> Usuarios</a>
            <?php endif; ?>
        </nav>
        <div class="user-info">
            <strong><?php echo $_SESSION["nombre"]; ?></strong><br>
            <a href="logout.php" class="btn btn-sm btn-warning mt-2">Cerrar Sesión</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title">Gestión de Usuarios</h2>
            <a href="../index.php" target="_blank" class="btn btn-outline-secondary">Ver Sitio Web <i class="fas fa-external-link-alt ms-2"></i></a>
        </header>

        <div class="container-fluid">
            <div class="row">
                <!-- Formulario para crear usuario -->
                <div class="col-lg-5">
                    <div class="card p-4">
                        <h4 class="mb-4">Añadir Nuevo Usuario</h4>
                        <form action="procesar_usuario.php" method="POST">
                            <div class="mb-3"><label for="nombres" class="form-label">Nombre Completo</label><input type="text" class="form-control" id="nombres" name="nombres" required></div>
                            <div class="mb-3"><label for="users" class="form-label">Nombre de Usuario</label><input type="text" class="form-control" id="users" name="users" required></div>
                            <div class="mb-3"><label for="clave" class="form-label">Contraseña</label><input type="password" class="form-control" id="clave" name="clave" required></div>
                            <div class="mb-3"><label for="nivel" class="form-label">Nivel de Acceso</label><select class="form-select" id="nivel" name="nivel" required><option value="2">Editor</option><option value="1">Administrador</option></select></div>
                            <div class="form-check form-switch mb-4"><input class="form-check-input" type="checkbox" id="estado" name="estado" value="1" checked><label class="form-check-label" for="estado">Usuario Activo</label></div>
                            <button type="submit" class="btn-submit w-100 p-2">Crear Usuario</button>
                        </form>
                    </div>
                </div>

                <!-- Tabla de usuarios existentes -->
                <div class="col-lg-7">
                    <div class="card p-4">
                        <h4 class="mb-4">Usuarios Registrados</h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead><tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Nivel</th><th>Estado</th><th>Acciones</th></tr></thead>
                                <tbody>
                                    <?php foreach($usuarios_existentes as $usuario): ?>
                                    <tr>
                                        <td><?php echo $usuario['usersId']; ?></td>
                                        <td><?php echo htmlspecialchars($usuario['nombres']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['users']); ?></td>
                                        <td><?php echo $usuario['nivel'] == 1 ? 'Admin' : 'Editor'; ?></td>
                                        <td><span class="badge bg-<?php echo $usuario['estado'] ? 'success' : 'secondary'; ?>"><?php echo $usuario['estado'] ? 'Activo' : 'Inactivo'; ?></span></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="#" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
