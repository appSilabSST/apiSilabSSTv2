<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_risco']) && is_numeric($json['id_risco']) &&
            isset($json['id_exame']) && is_numeric($json['id_exame'])
        ) {
            $sql = "
            UPDATE rl_riscos_exames SET
            id_risco = :id_risco, 
            id_exame = :id_exame, 
            padronizar = :padronizar, 
            periodicidade = :periodicidade, 
            admissional = :admissional, 
            periodico = :periodico, 
            monitoracao_pontual = :monitoracao_pontual, 
            mudanca_risco = :mudanca_risco, 
            retorno_trabalho = :retorno_trabalho, 
            demissional = :demissional,
            data_edit = NOW()
            WHERE id_rl_setor_exame = :id_rl_setor_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_risco', trim($json['id_risco']));
            $stmt->bindParam(':id_exame', trim($json['id_exame']));
            $stmt->bindParam(':padronizar', trim($json['padronizar']), PDO::PARAM_INT);
            $stmt->bindParam(':periodicidade', trim($json['periodicidade']), PDO::PARAM_INT);
            $stmt->bindParam(':admissional', trim($json['admissional']), PDO::PARAM_INT);
            $stmt->bindParam(':periodico', trim($json['periodico']), PDO::PARAM_INT);
            $stmt->bindParam(':monitoracao_pontual', trim($json['monitoracao_pontual']), PDO::PARAM_INT);
            $stmt->bindParam(':mudanca_risco', trim($json['mudanca_risco']), PDO::PARAM_INT);
            $stmt->bindParam(':retorno_trabalho', trim($json['retorno_trabalho']), PDO::PARAM_INT);
            $stmt->bindParam(':demissional', trim($json['demissional']), PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(200);
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
                'result' => 'Exame já existente!',
                'error' => $th->getMessage()
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
