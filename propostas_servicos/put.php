<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_rl_proposta_servico']) && is_numeric($json['id_rl_proposta_servico'])) {
            $sql = "
            UPDATE rl_propostas_servicos SET
            id_servico = :id_servico, 
            valor = :valor,
            prazo = :prazo,
            observacoes = :observacoes
            WHERE id_rl_proposta_servico = :id_rl_proposta_servico
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_servico', trim($json['id_servico']));
            $stmt->bindParam(':valor', trim($json['valor']));
            $stmt->bindParam(':prazo', trim($json['prazo']));
            $stmt->bindParam(':observacoes', trim($json['observacoes']), isset($json['observacoes']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':id_rl_proposta_servico', trim($json['id_rl_proposta_servico']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Serviço atualizada com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao atualizar a Serviço!'
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
                'result' => 'Serviço já existente nesta proposta!'
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
