<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_empresa']) && is_numeric($json['id_empresa']) &&
            isset($json['id_status_documento']) && is_numeric($json['id_status_documento']) &&
            isset($json['id_local_atividade']) && is_numeric($json['id_local_atividade']) &&
            isset($json['id_profissional']) && is_numeric($json['id_profissional']) &&
            isset($json['data_inicio']) && isset($json['data_fim']) &&
            isset($json['responsavel']) && isset($json['responsavel_cpf']) && isset($json['responsavel_email'])
        ) {

            $sql = "
            INSERT INTO pgr (nr_pgr, id_empresa, grau_risco_empresa, id_status_documento, id_local_atividade, grau_risco_local_atividade, id_profissional, data_inicio, data_fim, responsavel, responsavel_cpf, responsavel_email) 
            SELECT 
            IF(((SELECT IFNULL(MAX(nr_pgr), 0) FROM pgr) - ((DATE_FORMAT(CURDATE(), '%y') * 100000))) >= 0,
                (SELECT MAX(nr_pgr) + 1 FROM pgr),
                (DATE_FORMAT(CURDATE(), '%y') * 100000 + 1)
            ),
            :id_empresa, :grau_risco_empresa, :id_status_documento, :id_local_atividade, :grau_risco_local_atividade, :id_profissional, :data_inicio, :data_fim, :responsavel, :responsavel_cpf, :responsavel_email
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':grau_risco_empresa', trim($json['grau_risco_empresa']));
            $stmt->bindParam(':id_status_documento', trim($json['id_status_documento']));
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':grau_risco_local_atividade', trim($json['grau_risco_local_atividade']));
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']));
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']));
            $stmt->bindParam(':data_fim', trim($json['data_fim']));
            $stmt->bindParam(':responsavel', trim($json['responsavel']));
            $stmt->bindParam(':responsavel_cpf', trim($json['responsavel_cpf']));
            $stmt->bindParam(':responsavel_email', trim($json['responsavel_email']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'PGR cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar PGR!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
