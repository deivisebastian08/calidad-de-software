<?php
require_once("adm/script/conex.php");

// Crear conexión
$cn = new MySQLcn();

// --- Función para detectar el sistema operativo ---
function detectarSO($ua) {
    $ua = strtolower($ua);
    if (strpos($ua, 'windows') !== false) return 'Windows';
    if (strpos($ua, 'linux') !== false) return 'Linux';
    if (strpos($ua, 'mac') !== false) return 'MacOS';
    if (strpos($ua, 'android') !== false) return 'Android';
    if (strpos($ua, 'iphone') !== false) return 'iOS';
    return 'Otro';
}

// --- REGISTRAR VISITA ---
$ip = $_SERVER['REMOTE_ADDR'];
$navegador = $_SERVER['HTTP_USER_AGENT'];
$so = detectarSO($navegador);
$fecha = date("Y-m-d");
$hora = date("H:i:s");

// Insertar la visita
$sql_insertar = "INSERT INTO visitas (ip, so, navegador, fecha, hora) 
                 VALUES ('$ip', '$so', '$navegador', '$fecha', '$hora')";
$cn->Query($sql_insertar);

// --- OBTENER BANNERS ACTIVOS ---
$sql_banners = "SELECT Titulo, Describir, Enlace, Imagen FROM banner WHERE estado = 1 ORDER BY fecha DESC";
$cn->Query($sql_banners);
$banners = $cn->Rows();

// Cerrar conexión
$cn->Close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calidad de Software</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .carousel-item {
            height: 500px;
            background-size: cover;
            background-position: center;
        }
        .carousel-caption {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
            max-width: 80%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <!-- Navigation Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Calidad de Software</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Noticias</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link" href="adm/index.php">Iniciar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Banner Slider -->
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php for($i = 0; $i < count($banners); $i++): ?>
                <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="<?php echo $i; ?>"
                    <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
            <?php endfor; ?>
        </div>

        <div class="carousel-inner">
            <?php if (!empty($banners)): ?>
                <?php foreach ($banners as $index => $banner): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="images/banner/<?php echo htmlspecialchars($banner['Imagen']); ?>" class="d-block w-100" alt="">
                        <div class="carousel-caption">
                            <h3><?php echo htmlspecialchars($banner['Titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($banner['Describir']); ?></p>
                            <?php if (!empty($banner['Enlace'])): ?>
                                <a href="<?php echo htmlspecialchars($banner['Enlace']); ?>" class="btn btn-primary" target="_blank">
                                    Ver más <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="carousel-item active">
                    <img src="images/banner/default.jpg" class="d-block w-100" alt="Default">
                    <div class="carousel-caption">
                        <h3>Bienvenido</h3>
                        <p>No hay banners activos en este momento.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 text-center">
        <p>&copy; <?php echo date("Y"); ?> Calidad de Software. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
