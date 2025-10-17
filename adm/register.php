<?php
// 1. VERIFICAR QUE SE HAYAN RECIBIDO DATOS POR POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 2. OBTENER Y SANITIZAR LOS DATOS
    $nombre = isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : '';
    $login = isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validar que los campos no estén vacíos
    if (!empty($nombre) && !empty($login) && !empty($password)) {
        // 3. CONECTAR A LA BASE DE DATOS
        $servidor = "localhost";
        $usuario_db = "root";
        $password_db = "";
        $base_de_datos = "myweb";
        $cn = new mysqli($servidor, $usuario_db, $password_db, $base_de_datos);

        if ($cn->connect_error) {
            $mensaje = "Error de conexión a la base de datos.";
        } else {
            // 4. VERIFICAR SI EL USUARIO YA EXISTE
            $sql_check = "SELECT idUsuario FROM usuarios WHERE login = ?";
            if ($stmt_check = $cn->prepare($sql_check)) {
                $stmt_check->bind_param("s", $login);
                $stmt_check->execute();
                $stmt_check->store_result();

                if ($stmt_check->num_rows > 0) {
                    $mensaje = "El nombre de usuario o email ya está registrado.";
                } else {
                    // 5. INSERTAR EL NUEVO USUARIO COMO PENDIENTE
                    $nivel_default = 'Colaborador';
                    $estado_default = 'Pendiente';
                    
                    // NOTA: Se guarda la contraseña en texto plano por compatibilidad con el sistema de login existente.
                    // La práctica recomendada es usar password_hash().
                    $sql_insert = "INSERT INTO usuarios (Nombre, login, password, nivel, estado) VALUES (?, ?, ?, ?, ?)";
                    if ($stmt_insert = $cn->prepare($sql_insert)) {
                        $stmt_insert->bind_param("sssss", $nombre, $login, $password, $nivel_default, $estado_default);
                        if ($stmt_insert->execute()) {
                            $mensaje = "¡Registro exitoso! Su cuenta está pendiente de aprobación por un administrador.";
                        } else {
                            $mensaje = "Error al registrar el usuario.";
                        }
                        $stmt_insert->close();
                    }
                }
                $stmt_check->close();
            }
            $cn->close();
        }
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
} else {
    $mensaje = "Método de solicitud no válido.";
}

// 6. REDIRIGIR DE VUELTA CON UN MENSAJE
header("Location: index.php?form=registro&mensaje=" . urlencode($mensaje));
exit();
?>
