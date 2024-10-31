<?php
try {
    if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
        $id_formulario = trim($_GET["id"]);
        $sql = "
        DELETE FROM formularios
        WHERE id_formulario = :id_formulario
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_formulario', $id_formulario);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            // Se houver um erro ao executar a declaração SQL
            $errorInfo = $stmt->errorInfo();
            $error = $errorInfo[2]; // Mensagem de erro específica
            $result = array("status" => "fail", "error" => $error);
            http_response_code(500); // Internal Server Error
            exit;
        }

        $result = array(
            'status' => 'success',
            'result' => 'Afastamento removido com sucesso!'
        );
    }
} catch (PDOException $ex) {
    $result = ["status" => "fail", "error" => $ex->getMessage()];
    http_response_code(200);
} catch (Exception $ex) {
    $result = ["status" => "fail", "error" => $ex->getMessage()];
    http_response_code(200);
} finally {
    $conn = null;
    echo json_encode($result);
}
