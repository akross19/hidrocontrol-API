<?php

    require_once '../conexion.php';
    require_once '../headers.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'));
        $result = json_encode(array('status' => 'error'));
        try {
            if($conn) {
                $conn->beginTransaction();
                $sqlStatement = $conn->prepare(
                    "SELECT * FROM usuario WHERE
                        (usuario = :usuario AND contrasena = :contrasena) AND estado = 1"
                );

                $sqlStatement->bindParam(':usuario', $data->usuario);
                $sqlStatement->bindParam(':contrasena', $data->contrasena);
                    
                if($sqlStatement->execute()) {
                    if(($result = $sqlStatement->rowCount()) > 0) {
                        $result = json_encode(array('status' => 'success'));
                    } else {
                        $result = json_encode(array('status' => 'error'));
                    }
                } else {
                    $result = json_encode(array('status' => 'error'));
                }

                $conn->commit();
            }
        } catch (PDOException $e) {
            $conn->rollBack();
        }
        exit($result);
    }

?>