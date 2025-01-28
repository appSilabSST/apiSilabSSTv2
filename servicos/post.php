<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['servico'])
        ) {

            $sql = "
            INSERT INTO servicos (servico) VALUES
            (:servico)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':servico', trim($json['servico']), PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = 'Serviço cadastrado com sucesso!';
            } else {
                http_response_code(500);
                $result = 'Falha ao cadastrar serviço!';
            }
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = 'Plano de ação já existente!';
        } else {
            $result = $th->getMessage();
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
