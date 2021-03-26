<?php
include_once 'config/dbh.php';
include_once 'config/cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    $tip = $data->tipo_usuario;
    $user = $data->usuario;
    $pass = $data->contrasena;

    // Hash Password
    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    // U can do validation like unique username etc....

    $sql = $conn->query("INSERT INTO usuario (tipo_usuario, usuario, contrasena, estado) VALUES ( '$tip','$user', '$hashed', DEFAULT)");
    if ($sql) {
        http_response_code(201);
        echo json_encode(array('message' => 'User created'));
    } else {
        http_response_code(500);
        echo json_encode(array('message' => 'Internal Server error'));
    }
} else {
    http_response_code(404);
}