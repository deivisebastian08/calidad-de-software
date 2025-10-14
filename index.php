<?php
// Agregando seguimiento de visitas y obteniendo banners y noticias con mysqli

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

// Inicializar arrays para evitar errores
$banners = [];
$noticias = [];

if (!$cn->connect_error) {
    // --- REGISTRAR VISITA ---
    $sql_visita = "INSERT INTO visitas (ip, so, navegador, fecha, hora) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $cn->prepare($sql_visita)) {
        $stmt->bind_param("sssss", $ip, $so, $navegador, $fecha, $hora);
        $stmt->execute();
        $stmt->close();
    }

    // --- OBTENER BANNERS ACTIVOS ---
    $sql_banners = "SELECT Titulo, Describir, Enlace, Imagen FROM banner WHERE estado = 1 ORDER BY fecha DESC";
    $result_banners = $cn->query($sql_banners);
    if ($result_banners && $result_banners->num_rows > 0) {
        while($row = $result_banners->fetch_assoc()) {
            $banners[] = $row;
        }
    }

    // --- OBTENER ÚLTIMAS NOTICIAS ---
    $sql_noticias = "SELECT titulo, nota, imagen FROM noticias WHERE estado = 1 ORDER BY fecha DESC LIMIT 4";
    $result_noticias = $cn->query($sql_noticias);
    if ($result_noticias && $result_noticias->num_rows > 0) {
        while($row = $result_noticias->fetch_assoc()) {
            $noticias[] = $row;
        }
    }

    $cn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calidad de Software - QA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts para la caligrafía -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Segoe+UI&display=swap" rel="stylesheet">
    <style>
        /* Variables */
        :root {
            --primary-blue: #1e3a5f;
            --accent-orange: #ff8c42;
            --light-gray: #f5f7fa;
            --text-dark: #1a202c;
            --text-gray: #4a5568;
            --font-body: 'Segoe UI', 'Arial', sans-serif;
            --font-script: 'Dancing Script', cursive;
        }

        html { scroll-behavior: smooth; }
        body { font-family: var(--font-body); background: var(--light-gray); color: var(--text-dark); line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* Navegación */
        .navbar-custom { background: var(--primary-blue); padding: 1rem 0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-custom .container { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-family: var(--font-script); font-size: 2.2rem; font-weight: 700; color: white; text-decoration: none; text-transform: capitalize; }
        .nav-menu { display: flex; list-style: none; gap: 2.5rem; margin-bottom: 0; }
        .nav-menu a { color: rgba(255,255,255,0.9); text-decoration: none; font-size: 0.95rem; font-weight: 500; }
        .nav-menu a:hover { color: var(--accent-orange); }

        /* Carrusel de Banners */
        .carousel-item { height: 60vh; min-height: 400px; background-size: cover; background-position: center; }
        .carousel-caption { background: rgba(30, 58, 95, 0.7); padding: 2rem; border-radius: 12px; max-width: 70%; margin: 0 auto 3rem; border-left: 4px solid var(--accent-orange); }
        .carousel-caption h3 { font-family: var(--font-script); font-size: 3rem; }

        /* Secciones */
        .section { padding: 90px 0; }
        .section-white { background: white; }
        .section-title { font-family: var(--font-script); font-size: 3.8rem; text-align: center; margin-bottom: 1.5rem; color: var(--primary-blue); font-weight: 700; }
        .section-description { text-align: center; max-width: 750px; margin: 0 auto 4rem; color: var(--text-gray); font-size: 1.15rem; }

        /* Grid de Tarjetas */
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-top: 3rem; }
        
        /* Tarjetas de Noticias */
        .card-news { background: white; border-radius: 12px; border-left: 4px solid var(--accent-orange); box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden; display: flex; flex-direction: column; transition: all 0.3s; }
        .card-news:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
        .card-news img { height: 200px; object-fit: cover; width: 100%; }
        .card-news-body { padding: 1.5rem; }
        .card-news h5 { font-family: var(--font-script); font-size: 2rem; color: var(--primary-blue); font-weight: 700; margin-bottom: 0.75rem; }
        .card-news p { font-family: var(--font-body); color: var(--text-gray); font-size: 0.95rem; }

        /* Tarjetas de Servicios */
        .service-card { background: white; padding: 2.5rem 2rem; border-radius: 12px; border-top: 4px solid var(--accent-orange); box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center; transition: all 0.3s; }
        .service-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
        .service-icon { font-size: 3rem; color: var(--accent-orange); margin-bottom: 1.5rem; }
        .service-card h4 { font-family: var(--font-script); font-size: 2rem; margin-bottom: 1rem; color: var(--primary-blue); font-weight: 700; }

        /* Footer */
        .footer { background: var(--primary-blue); padding: 2.5rem 0; text-align: center; margin-top: 5rem; }
        .footer p { color: rgba(255,255,255,0.9); font-size: 0.95rem; }
    </style>
</head>
<body>
    <nav class="navbar-custom">
        <div class="container">
            <a href="index.php" class="logo">QA Software</a>
            <ul class="nav-menu">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="noticias.php">Noticias</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="adm/index.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Banner Slider Dinámico -->
    <header id="inicio">
        <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php for($i = 0; $i < count($banners); $i++): ?>
                    <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="<?php echo $i; ?>" <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
                <?php endfor; ?>
            </div>
            <div class="carousel-inner">
                <?php if (!empty($banners)): ?>
                    <?php foreach ($banners as $index => $banner): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" style="background-image: url('images/banner/<?php echo htmlspecialchars($banner['Imagen']); ?>')">
                            <div class="carousel-caption d-none d-md-block">
                                <h3><?php echo htmlspecialchars($banner['Titulo']); ?></h3>
                                <p><?php echo htmlspecialchars($banner['Describir']); ?></p>
                                <?php if (!empty($banner['Enlace'])): ?>
                                    <a href="<?php echo htmlspecialchars($banner['Enlace']); ?>" class="btn btn-warning" target="_blank">Ver más</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="carousel-item active" style="background-image: url('https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=2070&auto=format&fit=crop')">
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Bienvenido a QA Software</h3>
                            <p>Asegurando la excelencia en cada línea de código.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
        </div>
    </header>

    <!-- Sección de Noticias Dinámica -->
    <section class="section" id="noticias">
        <div class="container">
            <h2 class="section-title">Últimas Noticias</h2>
            <p class="section-description">Mantente al día con las últimas tendencias, herramientas y avances en el mundo del desarrollo y la calidad de software.</p>
            <div class="cards-grid">
                <?php if (!empty($noticias)): ?>
                    <?php foreach ($noticias as $noticia): ?>
                        <div class="card-news">
                            <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            <div class="card-news-body">
                                <h5><?php echo htmlspecialchars($noticia['titulo']); ?></h5>
                                <p><?php echo htmlspecialchars($noticia['nota']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12"><p class="text-center">No hay noticias disponibles.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Sección de Servicios -->
    <section class="section section-white" id="servicios">
        <div class="container">
            <h2 class="section-title">Nuestros Servicios</h2>
            <p class="section-description">Ofrecemos un conjunto completo de servicios de QA para garantizar que su software cumpla con los más altos estándares de calidad.</p>
            <div class="cards-grid">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-tasks"></i></div>
                    <h4>Pruebas Funcionales</h4>
                    <p>Verificamos que cada función de su aplicación opere conforme a sus requisitos, asegurando una experiencia de usuario impecable.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-rocket"></i></div>
                    <h4>Pruebas de Rendimiento</h4>
                    <p>Analizamos la velocidad, escalabilidad y estabilidad de su aplicación bajo carga para garantizar un rendimiento óptimo.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-shield-alt"></i></div>
                    <h4>Auditoría de Seguridad</h4>
                    <p>Identificamos vulnerabilidades y riesgos de seguridad en su software para proteger sus datos y la confianza de sus usuarios.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-cogs"></i></div>
                    <h4>Automatización de Pruebas</h4>
                    <p>Desarrollamos scripts de prueba automatizados para acelerar el ciclo de testing y mejorar la cobertura y precisión.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Calidad de Software. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
