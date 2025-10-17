<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['login'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado']);
    exit();
}

require_once("script/conex.php");
$cn = new MySQLcn();

$entity = $_GET['entity'] ?? '';
$action = $_REQUEST['action'] ?? '';

switch ($entity) {
    case 'stats':
        $cn->Query("SELECT COUNT(*) FROM usuarios");
        $users = $cn->FetRows()[0];
        $cn->Query("SELECT COUNT(*) FROM banner");
        $banners = $cn->FetRows()[0];
        $cn->Query("SELECT COUNT(*) FROM noticias");
        $news = $cn->FetRows()[0];
        echo json_encode(['status' => 'success', 'data' => ['users' => $users, 'banners' => $banners, 'news' => $news]]);
        break;

    case 'users':
        if ($_SESSION['nivel'] != 'Administrador') break;
        // Lógica para usuarios
        break;

    case 'banners':
        // Lógica para banners
        break;

    case 'news':
        // Lógica para noticias
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Entidad no reconocida']);
        break;
}

$cn->Close();
?>