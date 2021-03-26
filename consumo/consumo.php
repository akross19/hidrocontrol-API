<?php

    require_once '../conexion.php';
    require_once '../headers.php';

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

                $sqlStatement->bindParam(':cantidadAgua', $data->consumo);
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

?>