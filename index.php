<?php
// Agregando seguimiento de visitas y obteniendo banners con mysqli

// --- Función para detectar el sistema operativo ---
function detectarSO($sua) {
    $sua = strtolower($sua);
    if (strpos($sua, 'windows') !== false) return 'Windows';
    if (strpos($sua, 'linux') !== false) return 'Linux';
    if (strpos($sua, 'mac') !== false) return 'MacOS';
    if (strpos($sua, 'android') !== false) return 'Android';
    if (strpos($sua, 'iphone') !== false) return 'iOS';
    return 'Otro';
}

// --- Datos de la visita ---
$ip = $_SERVER['REMOTE_ADDR'];
$navegador = $_SERVER['HTTP_USER_AGENT'];
$so = detectarSO($navegador);
$fecha = date("Y-m-d");
$hora = date("H:i:s");

// --- Conexión a la base de datos ---
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_de_datos = "myweb";

$cn = new mysqli($servidor, $usuario, $password, $base_de_datos);

// Inicializar banners como un array vacío para evitar errores si la conexión falla
$banners = [];

// Verificar conexión y proceder si tiene éxito
if (!$cn->connect_error) {
    // --- REGISTRAR VISITA (Usando Consultas Preparadas para seguridad) ---
    $sql_visita = "INSERT INTO visitas (ip, so, navegador, fecha, hora) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $cn->prepare($sql_visita)) {
        // Vincular parámetros: s = string
        $stmt->bind_param("sssss", $ip, $so, $navegador, $fecha, $hora);
        // Ejecutar la consulta
        $stmt->execute();
        // Cerrar la sentencia
        $stmt->close();
    }

    // --- OBTENER BANNERS ACTIVOS ---
    $sql_banners = "SELECT Titulo, Describir, Enlace, Imagen FROM banner WHERE estado = 1 ORDER BY fecha DESC";
    $result_banners = $cn->query($sql_banners);
    
    if ($result_banners && $result_banners->num_rows > 0) {
        // Almacenar los resultados en el array de banners
        while($row = $result_banners->fetch_assoc()) {
            $banners[] = $row;
        }
    }

    // Cerrar Conexión
    $cn->close();
}
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
        .news-section {
            padding: 60px 0;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
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

    <!-- News Section -->
    <section class="news-section">
        <div class="container">
            <h2 class="text-center mb-5">Últimas Noticias</h2>
            <div class="row">
                <!-- News Item 1 -->
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=2070&auto=format&fit=crop" class="card-img-top" alt="Noticia 1">
                        <div class="card-body">
                            <h5 class="card-title">Avances en IA</h5>
                            <p class="card-text">Descubre cómo la inteligencia artificial está revolucionando la industria del software.</p>
                        </div>
                    </div>
                </div>
                <!-- News Item 2 -->
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=2070&auto=format&fit=crop" class="card-img-top" alt="Noticia 2">
                        <div class="card-body">
                            <h5 class="card-title">Desarrollo Web Moderno</h5>
                            <p class="card-text">Las últimas tendencias y frameworks para crear aplicaciones web de alto impacto.</p>
                        </div>
                    </div>
                </div>
                <!-- News Item 3 -->
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=2070&auto=format&fit=crop" class="card-img-top" alt="Noticia 3">
                        <div class="card-body">
                            <h5 class="card-title">Seguridad Informática</h5>
                            <p class="card-text">Protege tus proyectos con las mejores prácticas de ciberseguridad del 2024.</p>
                        </div>
                    </div>
                </div>
                <!-- News Item 4 -->
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="https://images.unsplash.com/photo-1542626991-a2f575a7e6a6?q=80&w=2070&auto=format&fit=crop" class="card-img-top" alt="Noticia 4">
                        <div class="card-body">
                            <h5 class="card-title">Gestión de Proyectos</h5>
                            <p class="card-text">Metodologías ágiles para entregar software de calidad a tiempo y dentro del presupuesto.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 text-center">
        <p>&copy; <?php echo date("Y"); ?> Calidad de Software. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
