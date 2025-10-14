<?php
session_start();
// 1. VERIFICACIÓN DE ACCESO
if(!isset($_SESSION['login'])){
    header("location:login.php?mensaje=Acceso+denegado");
    exit();
}

// 2. VERIFICAR QUE SE RECIBEN DATOS POR POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 3. RECOGER DATOS DEL FORMULARIO
    $titulo = trim($_POST['titulo']);
    $nota = trim($_POST['nota']);
    $estado = isset($_POST['estado']) ? 1 : 0;

    // Validar campos de texto
    if (empty($titulo) || empty($nota)) {
        header("location:gestionar_noticias.php?error=El+título+y+la+nota+son+obligatorios");
        exit();
    }

    // 4. MANEJO DE LA SUBIDA DE IMAGEN
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $target_dir = "../images/noticias/"; // Directorio donde se guardarán las imágenes
        // Crear el directorio si no existe
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = basename($_FILES["imagen"]["name"]);
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $unique_image_name = uniqid('noticia_', true) . '.' . $image_ext;
        $target_file = $target_dir . $unique_image_name;
        $db_image_path = "images/noticias/" . $unique_image_name; // Ruta que se guardará en la DB

        // Mover el archivo subido
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
            // 5. INSERTAR EN LA BASE DE DATOS
            require_once("script/conex_mysqli_prepared.php");
            $cn = new MySQLcn();

            $sql = "INSERT INTO noticias (titulo, nota, imagen, estado, fecha) VALUES (?, ?, ?, ?, NOW())";
            $params = [$titulo, $nota, $db_image_path, $estado];
            $types = "sssi"; // s: string, i: integer

            $stmt = $cn->prepare($sql);
            if ($stmt) {
                $cn->bind_param($stmt, $types, ...$params);
                if ($cn->execute($stmt)) {
                    $mensaje = "Noticia+guardada+exitosamente";
                    header("location:gestionar_noticias.php?mensaje=".$mensaje);
                } else {
                    header("location:gestionar_noticias.php?error=Error+al+guardar+en+la+base+de+datos");
                }
                $cn->close_stmt($stmt);
            } else {
                header("location:gestionar_noticias.php?error=Error+en+la+consulta");
            }
            $cn->close();

        } else {
            header("location:gestionar_noticias.php?error=Error+al+subir+la+imagen");
        }
    } else {
        header("location:gestionar_noticias.php?error=Debe+seleccionar+una+imagen");
    }
} else {
    header("location:gestionar_noticias.php");
    exit();
}
?>
