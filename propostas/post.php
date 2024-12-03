<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_empresa']) && is_numeric($json['id_empresa']) &&
            isset($json['id_local_atividade']) && is_numeric($json['id_local_atividade']) &&
            isset($json['id_status_proposta']) && is_numeric($json['id_status_proposta'])
        ) {
            $sql = "
            INSERT INTO propostas (nr_proposta, data, renovacao, id_empresa, id_local_atividade, qtde_funcionarios, qtde_funcoes, responsavel, responsavel_cpf, responsavel_email, consideracoes_finais) VALUES
            (
            SELECT 
            IF(((SELECT IFNULL(MAX(nr_ltcat), 0) FROM ltcat) - ((DATE_FORMAT(CURDATE(), '%y') * 100000))) >= 0,
                (SELECT MAX(nr_ltcat) + 1 FROM ltcat),
                (DATE_FORMAT(CURDATE(), '%y') * 100000 + 1)
            ),
            :data, :renovacao, :id_empresa, :id_local_atividade, :qtde_funcionarios, :qtde_funcoes, :responsavel, :responsavel_cpf, :responsavel_email, :consideracoes_finais)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data', trim($json['data']));
            $stmt->bindParam(':renovacao', trim($json['renovacao']));
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':qtde_funcionarios', trim($json['qtde_funcionarios']), isset($json['qtde_funcionarios']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':qtde_funcoes', trim($json['qtde_funcoes']), isset($json['qtde_funcoes']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel', trim($json['responsavel']), isset($json['responsavel']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel_cpf', trim($json['responsavel_cpf']), isset($json['responsavel_cpf']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel_email', trim($json['responsavel_email']), isset($json['responsavel_email']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':consideracoes_finais', trim($json['consideracoes_finais']), isset($json['consideracoes_finais']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Proposta criada com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar a proposta!'
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
                'result' => 'Proposta já existente!'
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
