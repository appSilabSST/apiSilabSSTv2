<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['id_proposta']) && is_numeric($json['id_proposta']) &&
            isset($json['id_servico']) && is_numeric($json['id_servico']) &&
            isset($json['valor']) && is_numeric($json['valor']) &&
            isset($json['prazo']) && is_numeric($json['prazo'])
        ) {
            $sql = "
            UPDATE rl_propostas_servicos SET
            id_proposta = :id_proposta , 
            id_servico = :id_servico, 
            valor = :valor,
            prazo = :prazo
            WHERE id_rl_proposta_servico = :id_rl_proposta_servico
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_proposta', trim($json['id_proposta']));
            $stmt->bindParam(':id_servico', trim($json['id_servico']));
            $stmt->bindParam(':valor', trim($json['valor']));
            $stmt->bindParam(':prazo', trim($json['prazo']));
            $stmt->bindParam(':id_rl_proposta_servico', trim($json['id']));
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Serviço atualizado com sucesso!'
            );
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
