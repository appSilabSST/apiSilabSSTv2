<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['ativo']) && is_numeric($json['ativo']) &&
            isset($json['nome']) && isset($json['descricao'])
        ) {

            $sql = "
            INSERT INTO especialidades (nome, descricao, ativo) VALUES
            (:nome, :descricao, :ativo)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->bindParam(':ativo', trim($json['ativo']), PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result =  'Exame cadastrado com sucesso!';
            } else {
                http_response_code(500);
                $result =  'Falha ao cadastrar exame!';
            }
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        $result =  $th->getMessage();
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
