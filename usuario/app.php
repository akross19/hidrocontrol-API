<?php

    require_once '../conexion.php';
    require_once '../headers.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sqlStatement = $conn->prepare("SELECT * FROM usuario WHERE id_usuario = :id");
            $sqlStatement->bindParam(":id", $id);
            $sqlStatement->execute();
            $data = $sqlStatement->fetch(PDO::FETCH_ASSOC);
        }
        else {
            $data = array();
            $sqlStatement = $conn->query("SELECT * FROM usuario");
            while ($d = $sqlStatement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $d;
            }
        }
        exit(json_encode($data));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'));
        $result = json_encode(array('status' => 'error'));
        try {
            if($conn) {
                $conn->beginTransaction();
                $sqlStatement = $conn->prepare(
                    "INSERT INTO usuario VALUES (
                        DEFAULT,
                        :tipo,
                        :usuario,
                        :contrasena,
                        1
                    )"
                );

                $sqlStatement->bindParam(':tipo', $data->tipoUsuario);
                $sqlStatement->bindParam(':usuario', $data->usuario);
                $sqlStatement->bindParam(':contrasena', $data->contrasena);
                $sqlStatement->execute();

                $data->id = $conn->lastInsertId();
                $result = json_encode($data);
                $conn->commit();
            }
        } catch (PDOException $e) {
            $conn->rollBack();
        }
        exit($result);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $data = json_decode(file_get_contents('php://input'));
            $result = json_encode(array('status' => 'error'));
            try {
                if($conn) {
                    $conn->beginTransaction();
                    $sqlStatement = $conn->prepare(
                        "UPDATE usuario SET
                            tipo_usuario = :tipoUsuario,
                            correo = :usuario,
                            contrasena = :contrasena
                            WHERE id_usuario = :id"
                    );

                    $sqlStatement->bindParam(':tipoUsuario', $data->tipoUsuario);
                    $sqlStatement->bindParam(':usuario', $data->usuario);
                    $sqlStatement->bindParam(':contrasena', $data->contrasena);
                    $sqlStatement->bindParam(":id", $id);
                    
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
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            try {
                if($conn) {
                    $conn->beginTransaction();
                    $sqlStatement = $conn->prepare(
                        "DELETE FROM usuario WHERE
                            id_usuario = :id"
                    );

                    $sqlStatement->bindParam(":id", $id);
                    
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
    }
?>