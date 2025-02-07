<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_pgr']) && is_numeric($json['id_pgr'])) {
            $sql = "
            UPDATE pgr SET
            id_empresa = :id_empresa, 
            grau_risco_empresa = :grau_risco_empresa,
            id_local_atividade = :id_local_atividade, 
            id_empresa_local_atividade = :id_empresa_local_atividade, 
            grau_risco_local_atividade = :grau_risco_local_atividade,
            id_profissional = :id_profissional, 
            data_inicio = :data_inicio, 
            data_fim = :data_fim, 
            responsavel = :responsavel, 
            responsavel_cpf = :responsavel_cpf, 
            responsavel_email = :responsavel_email,
            plano_emergencia = :plano_emergencia,
            consideracoes_finais = :consideracoes_finais
            WHERE id_pgr = :id_pgr
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':grau_risco_empresa', trim($json['grau_risco_empresa']));
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':id_empresa_local_atividade', trim($json['id_empresa_local_atividade']));
            $stmt->bindParam(':grau_risco_local_atividade', trim($json['grau_risco_local_atividade']));
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']));
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']));
            $stmt->bindParam(':data_fim', trim($json['data_fim']));
            $stmt->bindParam(':responsavel', trim($json['responsavel']));
            $stmt->bindParam(':responsavel_cpf', trim($json['responsavel_cpf']));
            $stmt->bindParam(':responsavel_email', trim($json['responsavel_email']));
            $stmt->bindParam(':plano_emergencia', trim($json['plano_emergencia']), isset($json['plano_emergencia']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':consideracoes_finais', trim($json['consideracoes_finais']), isset($json['consideracoes_finais']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':id_pgr', trim($json['id_pgr']));
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'PGR atualizado com sucesso!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
