<?php

    require_once '../conexion.php';
    require_once '../headers.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sqlStatement = $conn->prepare("SELECT * FROM tinaco WHERE id_tinaco = :id");
            $sqlStatement->bindParam(":id", $id);
            $sqlStatement->execute();
            $data = $sqlStatement->fetch(PDO::FETCH_ASSOC);
        }
        else {
            $data = array();
            $sqlStatement = $conn->query("SELECT * FROM tinaco");
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
                    "INSERT INTO tinaco VALUES (
                        DEFAULT,
                        :numTinaco,
                        :cantidadAgua,
                        :idConsumo
                    )"
                );

                $sqlStatement->bindParam(':numTinaco', $data->numTinaco);
                $sqlStatement->bindParam(':cantidadAgua', $data->cantidadAgua);
                $sqlStatement->bindParam(':idConsumo', $data->idConsumo);
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
                        "UPDATE tinaco SET
                            num_tinaco = :numTinaco,
                            cant_agua_actual = :cantidadAgua,
                            consumo_id_registro = :idConsumo
                            WHERE id_tinaco = :id"
                    );

                    $sqlStatement->bindParam(':numTinaco', $data->numTinaco);
                    $sqlStatement->bindParam(':cantidadAgua', $data->cantidadAgua);
                    $sqlStatement->bindParam(':idConsumo', $data->idConsumo);
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
                        "DELETE FROM tinaco WHERE
                            id_tinaco = :id"
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