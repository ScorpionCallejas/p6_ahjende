<?php
// controlador.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "root";
$pass = "";
$database = "db_test";

$connection = mysqli_connect($host, $user, $pass, $database);
mysqli_set_charset($connection, "utf8");

function escape($str, $conn) {
    return mysqli_real_escape_string($conn, strip_tags(trim($str)));
}

function respuestaExito($data = null, $mensaje = 'Operación exitosa') {
    echo json_encode(['success' => true, 'message' => $mensaje, 'data' => $data]);
    exit;
}

function respuestaError($mensaje = 'Ocurrió un error') {
    echo json_encode(['success' => false, 'message' => $mensaje]);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'crear_archivo':
        $nom_arc = escape($_POST['nom_arc'], $connection);
        $des_arc = escape($_POST['des_arc'], $connection);

        if (empty($nom_arc) || empty($des_arc)) {
            respuestaError('Nombre y descripción son obligatorios');
        }

        if (!isset($_FILES['arc_arc']) || $_FILES['arc_arc']['error'] != 0) {
            respuestaError('Archivo no válido');
        }

        $archivo = $_FILES['arc_arc'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $nombreCifrado = 'ARC-' . strtoupper(md5(uniqid(mt_rand(), true))) . '.' . $ext;
        $ruta = 'archivos/' . $nombreCifrado;

        if (!is_dir('archivos')) mkdir('archivos', 0777, true);
        if (!move_uploaded_file($archivo['tmp_name'], $ruta)) {
            respuestaError('No se pudo guardar el archivo');
        }

        $stmt = mysqli_prepare($connection, "INSERT INTO archivo (arc_arc, nom_arc, des_arc, est_arc, for_arc) VALUES (?, ?, ?, 'Activo', ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $nombreCifrado, $nom_arc, $des_arc, $ext);
        if (!mysqli_stmt_execute($stmt)) {
            respuestaError('Error al guardar en base de datos');
        }
        mysqli_stmt_close($stmt);

        respuestaExito(null, 'Archivo guardado correctamente');
        break;

    case 'listar':
        $res = mysqli_query($connection, "SELECT * FROM archivo ORDER BY id_arc DESC");
        $html = '<table class="table table-bordered"><thead><tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Formato</th><th>Acciones</th></tr></thead><tbody>';
        while ($r = mysqli_fetch_assoc($res)) {
            $html .= "<tr><td>{$r['id_arc']}</td><td>{$r['nom_arc']}</td><td>{$r['des_arc']}</td><td>{$r['for_arc']}</td><td>".
                     "<button class='btn btn-warning btn-sm' onclick='editarArchivo({$r['id_arc']})'>Editar</button> ".
                     "<button class='btn btn-danger btn-sm' onclick='eliminarArchivo({$r['id_arc']})'>Eliminar</button></td></tr>";
        }
        $html .= '</tbody></table>';
        echo $html;
        break;

    case 'obtener':
        $id = intval($_POST['id']);
        $q = mysqli_query($connection, "SELECT * FROM archivo WHERE id_arc = $id");
        if ($f = mysqli_fetch_assoc($q)) {
            respuestaExito($f);
        } else {
            respuestaError('Archivo no encontrado');
        }
        break;

    case 'editar_archivo':
        $id = intval($_POST['id_arc']);
        $nom_arc = escape($_POST['nom_arc'], $connection);
        $des_arc = escape($_POST['des_arc'], $connection);

        if (empty($id) || empty($nom_arc) || empty($des_arc)) {
            respuestaError('Todos los campos son obligatorios');
        }

        $stmt = mysqli_prepare($connection, "UPDATE archivo SET nom_arc=?, des_arc=? WHERE id_arc=?");
        mysqli_stmt_bind_param($stmt, "ssi", $nom_arc, $des_arc, $id);
        if (!mysqli_stmt_execute($stmt)) {
            respuestaError('Error al actualizar archivo');
        }
        mysqli_stmt_close($stmt);

        respuestaExito(null, 'Archivo actualizado');
        break;

    case 'eliminar_archivo':
        $id = intval($_POST['id']);
        $q = mysqli_query($connection, "SELECT arc_arc FROM archivo WHERE id_arc = $id");
        if ($r = mysqli_fetch_assoc($q)) {
            if (file_exists("archivos/{$r['arc_arc']}")) {
                unlink("archivos/{$r['arc_arc']}");
            }
            mysqli_query($connection, "DELETE FROM archivo WHERE id_arc = $id");
            respuestaExito(null, 'Archivo eliminado');
        } else {
            respuestaError('Archivo no encontrado');
        }
        break;

    default:
        respuestaError('Acción inválida');
}
