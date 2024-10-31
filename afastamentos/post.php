<?php
$formulario = trim($json["formulario"]);
$descricao = trim($json["descricao"]);
$acesso = $json["acesso"];

try {

    $insert_formularios  = "INSERT INTO formularios (formulario, descricao , acesso) VALUES (:formulario, :descricao, :acesso)";

    $stmt = $conn->prepare($insert_formularios);
    $stmt->bindParam(':formulario', $formulario);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':acesso', $acesso);
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
