<?php
include_once 'config/dbh.php';
include_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;

include_once 'config/cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    $uname = $data->usuario;
    $pass = $data->contrasena;

    $sql = $conn->query("SELECT * FROM usuario WHERE usuario = '$uname'"); 
    if ($sql->num_rows > 0) {
        $user = $sql->fetch_assoc();
        if (password_verify($pass, $user['contrasena'])) {
            $key = "YOUR_SECRET_KEY";  // JWT KEY
            $payload = array(
                'tipo_usuario' => $user['tipo_usuario'],
                'usuario' => $user['usuario'],
                'contrasena' => $user['contrasena'],
                'estado' => $user['estado']
            );

            $token = JWT::encode($payload, $key);
            http_response_code(200);
            echo json_encode(array('token' => $token));
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Login Failed!'));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('message' => 'Login Failed!'));
    }

}

