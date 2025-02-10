<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_risco']) && is_numeric($json['id_risco'])) {
            $sql = "
            INSERT INTO planos_acao (id_risco, plano_acao, descricao) VALUES 
            (:id_risco, :plano_acao, :descricao)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_risco', trim($json['id_risco']));
            $stmt->bindParam(':plano_acao', trim($json['plano_acao']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Plano de ação criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o plano de ação!'
                );
            }
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Plano de ação já existente!'
            );
        } else {
            $result = array(
                'status' => 'fail',
                'result' => $th->getMessage()
            );
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
