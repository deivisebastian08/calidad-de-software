<?php
session_start();
// 1. VERIFICACIÓN DE ACCESO Y NIVEL
if(!isset($_SESSION['login']) || $_SESSION['nivel'] != 1){
    header("location:login.php?mensaje=Acceso+denegado");
    exit();
}

// 2. VERIFICAR QUE SE RECIBEN DATOS POR POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 3. RECOGER Y SANITIZAR DATOS
    $nombres = trim($_POST['nombres']);
    $users = trim($_POST['users']);
    $clave = trim($_POST['clave']); // En un sistema real, esto debería ser hasheado.
    $nivel = (int)$_POST['nivel'];
    $estado = isset($_POST['estado']) ? 1 : 0;

    // Validar que los campos no estén vacíos
    if (empty($nombres) || empty($users) || empty($clave) || !isset($nivel)) {
        header("location:gestionar_usuarios.php?error=Todos+los+campos+son+obligatorios");
        exit();
    }

    // 4. INSERTAR EN LA BASE DE DATOS
    require_once("script/conex_mysqli_prepared.php");
    $cn = new MySQLcn();

    $sql = "INSERT INTO usuarios (grupoId, nombres, users, clave, nivel, estado, fechaCreada) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $params = [1, $nombres, $users, $clave, $nivel, $estado]; // grupoId se asume como 1
    $types = "isssii"; // i: integer, s: string

    $stmt = $cn->prepare($sql);
    if ($stmt) {
        $cn->bind_param($stmt, $types, ...$params);
        if ($cn->execute($stmt)) {
            $mensaje = "Usuario+creado+exitosamente";
            header("location:gestionar_usuarios.php?mensaje=".$mensaje);
        } else {
            $error = "Error+al+crear+el+usuario";
            header("location:gestionar_usuarios.php?error=".$error);
        }
        $cn->close_stmt($stmt);
    } else {
        $error = "Error+en+la+preparación+de+la+consulta";
        header("location:gestionar_usuarios.php?error=".$error);
    }
    $cn->close();

} else {
    // Si no es POST, redirigir
    header("location:gestionar_usuarios.php");
    exit();
}
?>