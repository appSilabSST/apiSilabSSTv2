<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_ltcat']) && is_numeric($json['id_ltcat'])) {
            $sql = "
            UPDATE ltcat SET
                id_profissional = :id_profissional, 
                data_inicio = :data_inicio, 
                id_usuario = :id_usuario, 
                id_empresa = :id_empresa, 
                id_local_atividade = :id_local_atividade, 
                grau_risco = :grau_risco, 
                responsavel = :responsavel, 
                responsavel_cpf = :responsavel_cpf, 
                responsavel_email = :responsavel_email, 
                consideracoes_finais = :consideracoes_finais
            WHERE id_ltcat = :id_ltcat
            ";

            $stmt = $conn->prepare($sql);

            // Bind dos parâmetros para a query SQL
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']));
            $stmt->bindParam(':id_usuario', trim($json['id_usuario']));
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']));
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':grau_risco', trim($json['grau_risco']));
            $stmt->bindParam(':responsavel', trim($json['responsavel']));
            $stmt->bindParam(':responsavel_cpf', trim($json['responsavel_cpf']));
            $stmt->bindParam(':responsavel_email', trim($json['responsavel_email']));
            $stmt->bindParam(':consideracoes_finais', trim($json['consideracoes_finais']), isset($json['consideracoes_finais']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':id_ltcat', trim($json['id_ltcat']));

            // Executa a query
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'LTCAT atualizado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao atualizar o LTCAT ou nenhum dado foi alterado!'
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
                'result' => 'LTCAT já existente!'
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
