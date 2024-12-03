<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['ativo']) && is_numeric($json['ativo']) &&
            isset($json['id_especialidade']) && is_numeric($json['id_especialidade'])
        ) {
            $sql = "
            UPDATE especialidades SET
            nome = :nome, 
            ativo = :ativo, 
            descricao = :descricao,
            WHERE id_especialidade = :id_especialidade
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->bindParam(':ativo', trim($json['ativo']), PDO::PARAM_INT);
            $stmt->bindParam(':id_especialidade', trim($json['id_especialidade']), PDO::PARAM_INT);

            $stmt->execute();

            http_response_code(200);
            $result = 'Exame atualizado com sucesso!';
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
