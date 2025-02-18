<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_empresa']) && is_numeric($json['id_empresa']) &&
            isset($json['id_local_atividade']) && is_numeric($json['id_local_atividade']) &&
            isset($json['id_profissional']) && is_numeric($json['id_profissional']) &&
            isset($json['id_usuario']) && is_numeric($json['id_usuario']) &&
            isset($json['data_inicio']) &&  isset($json['data_fim']) && isset($json['grau_risco']) &&
            isset($json['responsavel']) && isset($json['responsavel_cpf']) && isset($json['responsavel_email'])
        ) {
            $sql = "
            INSERT INTO pcmso (nr_pcmso, id_profissional,id_usuario, data_inicio, data_fim ,id_empresa, id_local_atividade,grau_risco, responsavel, responsavel_cpf, responsavel_email, consideracoes_finais) 
            SELECT 
            IF(((SELECT IFNULL(MAX(nr_pcmso), 0) FROM pcmso) - ((DATE_FORMAT(CURDATE(), '%y') * 100000))) >= 0,
                (SELECT MAX(nr_pcmso) + 1 FROM pcmso),
                (DATE_FORMAT(CURDATE(), '%y') * 100000 + 1)
            ),:id_profissional,:id_usuario, :data_inicio,:data_fim, :id_empresa, :id_local_atividade,:grau_risco, :responsavel, :responsavel_cpf, :responsavel_email, :consideracoes_finais";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']));
            $stmt->bindParam(':id_usuario', trim($json['id_usuario']));
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']));
            $stmt->bindParam(':data_fim', trim($json['data_fim']));
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':grau_risco', trim($json['grau_risco']));
            $stmt->bindParam(':responsavel', trim($json['responsavel']));
            $stmt->bindParam(':responsavel_cpf', trim($json['responsavel_cpf']));
            $stmt->bindParam(':responsavel_email', trim($json['responsavel_email']));
            $stmt->bindParam(':consideracoes_finais', trim($json['consideracoes_finais']), isset($json['consideracoes_finais']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Pcmso de atividade criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o Pcmso de atividade!'
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
                'result' => 'Local de atividade já existente!'
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
