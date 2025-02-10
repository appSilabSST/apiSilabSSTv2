<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_pcmso']) && is_numeric($json['id_pcmso'])) {
            $sql = "
            UPDATE pcmso SET
                id_profissional = :id_profissional, 
                data_inicio = :data_inicio, 
                id_empresa = :id_empresa, 
                id_local_atividade = :id_local_atividade, 
                grau_risco_empresa = :grau_risco_empresa, 
                grau_risco_local_atividade = :grau_risco_local_atividade, 
                responsavel = :responsavel, 
                responsavel_cpf = :responsavel_cpf, 
                responsavel_email = :responsavel_email, 
                consideracoes_finais = :consideracoes_finais,
                relatorio_analitico = :relatorio_analitico
            WHERE id_pcmso = :id_pcmso
            ";

            $stmt = $conn->prepare($sql);

            // Bind dos parâmetros para a query SQL
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']));
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']), isset($json['data_inicio']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':grau_risco_empresa', trim($json['grau_risco_empresa']), isset($json['grau_risco_empresa']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':grau_risco_local_atividade', trim($json['grau_risco_local_atividade']), isset($json['grau_risco_local_atividade']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel', trim($json['responsavel']), isset($json['responsavel']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel_cpf', trim($json['responsavel_cpf']), isset($json['responsavel_cpf']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel_email', trim($json['responsavel_email']), isset($json['responsavel_email']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':consideracoes_finais', trim($json['consideracoes_finais']), isset($json['consideracoes_finais']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':relatorio_analitico', trim($json['relatorio_analitico']), isset($json['relatorio_analitico']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':id_pcmso', trim($json['id_pcmso']));

            // Executa a query
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'pcmso atualizado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao atualizar o pcmso ou nenhum dado foi alterado!'
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
                'result' => 'pcmso já existente!'
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
