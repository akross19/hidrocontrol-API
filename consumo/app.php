<?php

    require_once '../conexion.php';
    require_once '../headers.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sqlStatement = $conn->prepare("SELECT * FROM consumo WHERE id_registro = :id");
            $sqlStatement->bindParam(":id", $id);
            $sqlStatement->execute();
            $data = $sqlStatement->fetch(PDO::FETCH_ASSOC);
        }
        else {
            $data = array();
            $sqlStatement = $conn->query("SELECT * FROM consumo");
            while ($d = $sqlStatement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $d;
            }
        }
        exit(json_encode($data));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'));
        $fecha = new \DateTime("now", new \DateTimeZone("UTC"));
        $fecha->sub(new DateInterval('PT6H'));
        $fechaActual = $fecha->format('Y-m-d H:i:s');
        $result = json_encode(array('status' => 'error'));
        try {
            if($conn) {
                $conn->beginTransaction();
                $sqlStatement = $conn->prepare(
                    "INSERT INTO consumo VALUES (
                        DEFAULT,
                        :cantidadAgua,
                        :fecha
                    )"
                );

                $sqlStatement->bindParam(':cantidadAgua', $data->cantidadAgua);
                $sqlStatement->bindParam(':fecha', $fechaActual);
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
            $fecha = new \DateTime("now", new \DateTimeZone("UTC"));
            $fecha->sub(new DateInterval('PT6H'));
            $fechaActual = $fecha->format('Y-m-d H:i:s');
            $result = json_encode(array('status' => 'error'));
            try {
                if($conn) {
                    $conn->beginTransaction();
                    $sqlStatement = $conn->prepare(
                        "UPDATE consumo SET
                            cant_agua = :cantidadAgua,
                            fecha = :fecha
                            WHERE id_registro = :id"
                    );

                    $sqlStatement->bindParam(':cantidadAgua', $data->cantidadAgua);
                    $sqlStatement->bindParam(':fecha', $fechaActual);
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
                        "DELETE FROM consumo WHERE
                            id_registro = :id"
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