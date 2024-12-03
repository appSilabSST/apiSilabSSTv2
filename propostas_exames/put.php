<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['id_proposta']) && is_numeric($json['id_proposta']) &&
            isset($json['id_exame']) && is_numeric($json['id_exame']) &&
            isset($json['valor']) && is_numeric($json['valor'])
        ) {
            $sql = "
            UPDATE rl_propostas_exames SET
            id_proposta = :id_proposta , 
            id_exame = :id_exame, 
            valor = :valor
            WHERE id_rl_proposta_exame = :id_rl_proposta_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_proposta', trim($json['id_proposta']));
            $stmt->bindParam(':id_exame', trim($json['id_exame']));
            $stmt->bindParam(':valor', trim($json['valor']));
            $stmt->bindParam(':id_rl_proposta_exame', trim($json['id']));
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Exame atualizado com sucesso!'
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
                'result' => 'Exame já existente nesta proposta!'
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
