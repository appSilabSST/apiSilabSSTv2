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
            INSERT INTO rl_setores_exames (id_setor, id_pcmso, id_exame, periodicidade, ids_tipos_atendimento) 
            VALUES (:id_setor, :id_pcmso, :id_exame, :periodicidade, :ids_tipos_atendimento)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_setor', trim($json['id_setor']));
            $stmt->bindParam(':id_pcmso', trim($json['id_pcmso']));
            $stmt->bindParam(':id_exame', trim($json['id_exame']));
            $stmt->bindParam(':periodicidade', trim($json['periodicidade']), PDO::PARAM_INT);
            $stmt->bindParam(':ids_tipos_atendimento', json_encode($json['ids_tipos_atendimento']));
            
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Exame cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar exame!'
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
