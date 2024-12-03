<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['id_empresa']) && is_numeric($json['id_empresa']) &&
            isset($json['id_local_atividade']) && is_numeric($json['id_local_atividade']) &&
            isset($json['id_status_documento']) && is_numeric($json['id_status_documento']) &&
            isset($json['id_profissional']) && is_numeric($json['id_profissional'])
        ) {
            $sql = "
            INSERT INTO ltcat (nr_ltcat, id_profissional, data_inicio, id_empresa, id_local_atividade, id_status_documento, grau_risco_empresa, grau_risco_local_atividade, responsavel, responsavel_cpf, responsavel_email, consideracoes_finais, id_profissional) VALUES
            (
            SELECT 
            IF(((SELECT IFNULL(MAX(nr_ltcat), 0) FROM ltcat) - ((DATE_FORMAT(CURDATE(), '%y') * 100000))) >= 0,
                (SELECT MAX(nr_ltcat) + 1 FROM ltcat),
                (DATE_FORMAT(CURDATE(), '%y') * 100000 + 1)
            ),
            :id_profissional, :data_inicio, :id_empresa, :id_local_atividade, :id_status_documento, :grau_risco_empresa, :grau_risco_local_atividade, :responsavel, :responsavel_cpf, :responsavel_email, :consideracoes_finais, :id_profissional)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']));
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_status_documento', trim($json['id_status_documento']));
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']), isset($json['data_inicio']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':grau_risco_empresa', trim($json['grau_risco_empresa']), isset($json['grau_risco_empresa']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':grau_risco_local_atividade', trim($json['grau_risco_local_atividade']), isset($json['grau_risco_local_atividade']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel', trim($json['responsavel']), isset($json['responsavel']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel_cpf', trim($json['responsavel_cpf']), isset($json['responsavel_cpf']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel_email', trim($json['responsavel_email']), isset($json['responsavel_email']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':consideracoes_finais', trim($json['consideracoes_finais']), isset($json['consideracoes_finais']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Local de atividade criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o local de atividade!'
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
