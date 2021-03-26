<?php

    require_once '../conexion.php';
    require_once '../headers.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sqlStatement = $conn->prepare("SELECT * FROM historial WHERE id_historial = :id");
            $sqlStatement->bindParam(":id", $id);
            $sqlStatement->execute();
            $data = $sqlStatement->fetch(PDO::FETCH_ASSOC);
        }
        else {
            $data = array();
            $sqlStatement = $conn->query("SELECT * FROM historial");
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
                    "INSERT INTO historial VALUES (
                        DEFAULT,
                        :bomba_id_activacion
                    )"
                );

                $sqlStatement->bindParam(':bomba_id_activacion', $data->bomba_id_activacion);
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
                        "UPDATE historial SET
                            bomba_id_activacion = :bomba_id_activacion
                            WHERE id_historial = :id"
                    );

                    $sqlStatement->bindParam(':bomba_id_activacion', $data->bomba_id_activacion);
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
                        "DELETE FROM historial WHERE
                            id_historial = :id"
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