<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_rl_setor_exame']) && is_numeric($json['id_rl_setor_exame'])
        ) {
            $sql = "
            UPDATE rl_setores_exames SET
                id_setor = :id_setor, 
                id_pcmso = :id_pcmso, 
                id_exame = :id_exame, 
                periodicidade = :periodicidade, 
                ids_tipos_atendimento = :ids_tipos_atendimento,
                data_edit = NOW()
            WHERE id_rl_setor_exame = :id_rl_setor_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_setor', trim($json['id_setor']));
            $stmt->bindParam(':id_pcmso', trim($json['id_pcmso']));
            $stmt->bindParam(':id_exame', trim($json['id_exame']));
            $stmt->bindParam(':periodicidade', trim($json['periodicidade']), PDO::PARAM_INT);
            $stmt->bindParam(':ids_tipos_atendimento', json_encode($json['ids_tipos_atendimento']));
            $stmt->bindParam(':id_rl_setor_exame', trim($json['id_rl_setor_exame']));
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
