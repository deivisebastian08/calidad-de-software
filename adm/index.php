<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_GET['mensaje'])){
	$mensaje = urldecode($_GET['mensaje']);
}else{
	$mensaje = "Por favor, inicie sesión para continuar";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - QA Software</title>
    <!-- Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts para la caligrafía -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Segoe+UI&display=swap" rel="stylesheet">
    <!-- Script de validación del formulario -->
    <script language='JavaScript' type='text/javascript' src='js/generax.js'></script>
    <style>
        :root {
            --primary-blue: #1e3a5f;
            --accent-orange: #ff8c42;
            --light-gray: #f5f7fa;
            --text-dark: #1a202c;
            --text-gray: #4a5568;
            --font-body: 'Segoe UI', 'Arial', sans-serif;
            --font-script: 'Dancing Script', cursive;
            --danger-bg: #f8d7da;
            --danger-text: #721c24;
        }
        html, body { height: 100%; }
        body { font-family: var(--font-body); background: var(--light-gray); color: var(--text-dark); display: flex; flex-direction: column; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* Navegación */
        .navbar-custom { background: var(--primary-blue); padding: 1rem 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; }
        .navbar-custom .container { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-family: var(--font-script); font-size: 2.2rem; color: white; text-decoration: none; text-transform: capitalize; }
        .nav-menu { display: flex; list-style: none; gap: 2.5rem; margin-bottom: 0; }
        .nav-menu a { color: rgba(255,255,255,0.9); text-decoration: none; font-size: 0.95rem; font-weight: 500; }
        .nav-menu a:hover { color: var(--accent-orange); }

        /* Contenedor principal del Login */
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 0; }
        .login-card { background: white; width: 100%; max-width: 450px; padding: 3rem; border-radius: 12px; border-top: 4px solid var(--accent-orange); box-shadow: 0 8px 30px rgba(0,0,0,0.1); }
        .login-title { font-family: var(--font-script); font-size: 3rem; text-align: center; margin-bottom: 1rem; color: var(--primary-blue); }
        
        /* Mensaje de error/info */
        #mensaje { font-size: 0.95rem; text-align: center; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.5rem; background-color: var(--danger-bg); color: var(--danger-text); }
        
        /* Formulario */
        .form-group { margin-bottom: 1.5rem; position: relative; }
        .form-control { width: 100%; padding: 0.8rem 1rem 0.8rem 2.5rem; border: 1px solid #ccc; border-radius: 8px; transition: border-color 0.3s; }
        .form-control:focus { border-color: var(--accent-orange); outline: none; box-shadow: 0 0 0 3px rgba(255, 140, 66, 0.2); }
        .input-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #aaa; }
        .captcha-img { border-radius: 8px; vertical-align: middle; }
        .btn-login { background: var(--accent-orange); color: white; width: 100%; padding: 0.9rem; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background 0.3s; }
        .btn-login:hover { background: #e67e22; }

        /* Footer */
        .footer { background: var(--primary-blue); padding: 2.5rem 0; text-align: center; }
        .footer p { color: rgba(255,255,255,0.9); font-size: 0.95rem; margin: 0; }
    </style>
</head>
<body>
    <nav class="navbar-custom">
        <div class="container">
            <a href="../index.php" class="logo">QA Software</a>
            <ul class="nav-menu">
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="../noticias.php">Noticias</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="login-card">
            <h1 class="login-title">Iniciar Sesión</h1>
            <form name='sesion' action='login.php' onSubmit='return iniciar();' method='POST'>
                <div id="mensaje">
                    <?php echo $mensaje; ?>
                </div>

                <div class="form-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="users" class="form-control" placeholder="Nombre de Usuario" required>
                </div>

                <div class="form-group">
                    <i class="fas fa-key input-icon"></i>
                    <input type="password" name="pass" class="form-control" placeholder="Contraseña" required>
                </div>

                <div class="form-group">
                    <i class="fas fa-image input-icon"></i>
                    <input type="text" name="clave" class="form-control" placeholder="Escriba el texto de la imagen" required>
                    <img src='script/generax.php?img=true' class="captcha-img" alt="Captcha" style="margin-top: 10px;">
                </div>

                <input type="submit" name="button" class="btn-login" value="Ingresar">
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Calidad de Software. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
