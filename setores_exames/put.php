<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_setor']) && is_numeric($json['id_setor']) &&
            isset($json['id_pcmso']) && is_numeric($json['id_pcmso']) &&
            isset($json['id_exame']) && is_numeric($json['id_exame'])
        ) {
            $sql = "
            UPDATE rl_setores_exames SET
            id_setor = :id_setor, 
            id_pcmso = :id_pcmso, 
            id_exame = :id_exame, 
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
            $stmt->bindParam(':id_setor', trim($json['id_setor']));
            $stmt->bindParam(':id_pcmso', trim($json['id_pcmso']));
            $stmt->bindParam(':id_exame', trim($json['id_exame']));
            $stmt->bindParam(':periodicidade', trim($json['periodicidade']), trim($json['periodicidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':admissional', trim($json['admissional']), trim($json['admissional']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':periodico', trim($json['periodico']), trim($json['periodico']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':monitoracao_pontual', trim($json['monitoracao_pontual']), trim($json['monitoracao_pontual']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':mudanca_risco', trim($json['mudanca_risco']), trim($json['mudanca_risco']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':retorno_trabalho', trim($json['retorno_trabalho']), trim($json['retorno_trabalho']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':demissional', trim($json['demissional']), trim($json['demissional']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_rl_setor_funcao', trim($json['id']));
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
