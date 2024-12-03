<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['severidade']) && is_string($json['severidade']) &&
            isset($json['codigo']) && is_numeric($json['codigo']) &&
            isset($json['tempo_exposicao']) && is_string($json['tempo_exposicao'])
        ) {
            $sql = "
            UPDATE severidades SET
            severidade = :severidade,
            codigo = :codigo,
            tempo_exposicao = :tempo_exposicao
            WHERE id_severidade = :id_severidade
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':severidade', trim($json['severidade']), PDO::PARAM_STR);
            $stmt->bindParam(':codigo', trim($json['codigo']), PDO::PARAM_INT);
            $stmt->bindParam(':tempo_exposicao', trim($json['tempo_exposicao']), PDO::PARAM_STR);
            $stmt->bindParam(':id_severidade', trim($json['id']), PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(200);
            $result = 'Severidade atualizada com sucesso!';
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = 'Severidade já existente!';
        } else {
            $result = $th->getMessage();
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
