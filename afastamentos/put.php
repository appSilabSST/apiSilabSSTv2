<?php
$id_formulario = $json["id_formulario"];
$formulario = trim($json["formulario"]);
$descricao = trim($json["descricao"]);
$acesso = $json["acesso"];
$ordem = $json["ordem"];
$exibir = $json["exibir"];

try {
    if (isset($exibir) && is_numeric($exibir)) {
        $sql = "
            UPDATE formularios SET
            exibir = IF(exibir = 1, 0, 1)
            WHERE id_formulario = :id_formulario
            ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_formulario', $id_formulario);
    } else {
        $sql = "
            UPDATE formularios SET
            formulario = :formulario,
            descricao = :descricao,
            acesso = :acesso,
            ordem = :ordem
            WHERE id_formulario = :id_formulario
            ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':formulario', $formulario);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':acesso', $acesso);
        $stmt->bindParam(':ordem', $ordem);
        $stmt->bindParam(':id_formulario', $id_formulario);
    }

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
        'result' => 'Formulário salvo com sucesso!'
    );
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
