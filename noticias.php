<?php
// --- Conexión a la base de datos y obtención de noticias ---
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_de_datos = "myweb";

$cn = new mysqli($servidor, $usuario, $password, $base_de_datos);

$noticias = [];
$noticia_destacada = null;

if (!$cn->connect_error) {
    // --- OBTENER TODAS LAS NOTICIAS ACTIVAS ---
    $sql_noticias = "SELECT titulo, nota, imagen FROM noticias WHERE estado = 1 ORDER BY fecha DESC";
    $result_noticias = $cn->query($sql_noticias);
    if ($result_noticias && $result_noticias->num_rows > 0) {
        while($row = $result_noticias->fetch_assoc()) {
            $noticias[] = $row;
        }
    }
    $cn->close();
}

// Separar la noticia destacada (la primera) del resto
if (!empty($noticias)) {
    $noticia_destacada = array_shift($noticias);
}

// --- SI NO HAY NOTICIAS EN LA BD, MOSTRAR DATOS DE EJEMPLO ---
if (empty($noticia_destacada) && empty($noticias)) {
    $noticia_destacada = [
        'titulo' => 'El Futuro de las Pruebas de Software',
        'nota' => 'La inteligencia artificial está revolucionando la forma en que probamos el software, automatizando tareas complejas y prediciendo defectos antes de que ocurran.',
        'imagen' => 'https://images.unsplash.com/photo-1555066931-4365d1469c98?q=80&w=2070&auto=format&fit=crop'
    ];
    $noticias = [
        [
            'titulo' => 'Principales Herramientas de QA en 2024',
            'nota' => 'Un recorrido por las herramientas más populares y efectivas para la automatización de pruebas, gestión de casos de prueba y seguimiento de errores.',
            'imagen' => 'https://images.unsplash.com/photo-1516116216624-53e697fedbea?q=80&w=1935&auto=format&fit=crop'
        ],
        [
            'titulo' => 'La Importancia de las Pruebas de Seguridad',
            'nota' => 'En un mundo digital, proteger los datos de los usuarios es primordial. Descubre por qué las pruebas de seguridad no son una opción, sino una necesidad.',
            'imagen' => 'https://images.unsplash.com/photo-1562813733-b31f71025d54?q=80&w=2069&auto=format&fit=crop'
        ],
        [
            'titulo' => 'Metodologías Ágiles y QA',
            'nota' => 'Cómo integrar eficazmente los procesos de aseguramiento de la calidad en equipos que trabajan con Scrum, Kanban y otras metodologías ágiles.',
            'imagen' => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?q=80&w=2070&auto=format&fit=crop'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - Calidad de Software</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .navbar-custom { background: var(--primary-blue); padding: 1rem 0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-custom .container { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-family: var(--font-script); font-size: 2.2rem; font-weight: 700; color: white; text-decoration: none; text-transform: capitalize; }
        .nav-menu { display: flex; list-style: none; gap: 2.5rem; margin-bottom: 0; }
        .nav-menu a { color: rgba(255,255,255,0.9); text-decoration: none; font-size: 0.95rem; font-weight: 500; }
        .nav-menu a:hover { color: var(--accent-orange); }
        
        /* Banner de Noticia Destacada */
        .featured-news-banner { position: relative; height: 50vh; background-size: cover; background-position: center; color: white; display: flex; align-items: center; text-align: center; }
        .featured-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(30, 58, 95, 0.75); }
        .featured-news-banner .container { position: relative; z-index: 2; }
        .featured-tag { background: var(--accent-orange); color: white; padding: 0.3rem 0.8rem; border-radius: 5px; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; font-family: var(--font-body); }
        .featured-news-banner h1 { font-family: var(--font-script); font-size: 4.5rem; font-weight: 700; margin: 1rem 0; line-height: 1.2; }
        .featured-news-banner p { font-family: var(--font-body); font-size: 1.2rem; max-width: 700px; margin: 0 auto; opacity: 0.9; }

        .section { padding: 90px 0; }
        .section-title { font-family: var(--font-script); font-size: 3.8rem; text-align: center; margin-bottom: 1.5rem; color: var(--primary-blue); font-weight: 700; }
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-top: 3rem; }
        .card-news { background: white; border-radius: 12px; border-left: 4px solid var(--accent-orange); box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden; display: flex; flex-direction: column; transition: all 0.3s; }
        .card-news:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
        .card-news img { height: 200px; object-fit: cover; width: 100%; }
        .card-news-body { padding: 1.5rem; }
        .card-news h5 { font-family: var(--font-script); font-size: 2rem; color: var(--primary-blue); font-weight: 700; margin-bottom: 0.75rem; }
        .card-news p { font-family: var(--font-body); color: var(--text-gray); font-size: 0.95rem; }
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
                <li><a href="adm/index.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <?php if ($noticia_destacada): ?>
    <header class="featured-news-banner" style="background-image: url('<?php echo htmlspecialchars($noticia_destacada['imagen']); ?>');">
        <div class="featured-overlay">
            <div class="container">
                <span class="featured-tag">Noticia Destacada</span>
                <h1><?php echo htmlspecialchars($noticia_destacada['titulo']); ?></h1>
                <p><?php echo htmlspecialchars($noticia_destacada['nota']); ?></p>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <main class="section">
        <div class="container">
            <?php if ($noticia_destacada): ?>
                <h2 class="section-title">Más Noticias</h2>
            <?php else: ?>
                <h2 class="section-title">Archivo de Noticias</h2>
            <?php endif; ?>

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
                <?php elseif (!$noticia_destacada): ?>
                    <div class="col-12">
                        <p class="text-center">No hay noticias para mostrar en este momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Calidad de Software. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
