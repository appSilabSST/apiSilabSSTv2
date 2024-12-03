<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['servico'])
        ) {
            $sql = "
            UPDATE servicos SET
            servico = :servico
            WHERE id_servico = :id_servico
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':servico', trim($json['servico']), PDO::PARAM_STR);
            $stmt->bindParam(':id_servico', trim($json['id']), PDO::PARAM_INT);;
            $stmt->execute();

            http_response_code(200);
            $result = 'Serviço atualizado com sucesso!';
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = 'Serviço já existente!';
        } else {
            $result = $th->getMessage();
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
