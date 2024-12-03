<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['razao_social']) && isset($json['tipo_inscricao']) && isset($json['nr_inscricao'])
        ) {
            $sql = "
            UPDATE locais_atividade SET
            id_empresa = :id_empresa, 
            id_tipo_ambiente = :id_tipo_ambiente, 
            razao_social = :razao_social, 
            tipo_inscricao = :tipo_inscricao, 
            nr_inscricao = :nr_inscricao, 
            logradouro = :logradouro, 
            numero = :numero, 
            complemento = :complemento, 
            bairro = :bairro, 
            cidade = :cidade, 
            uf = :uf, 
            cep = :cep, 
            cnae = :cnae, 
            atividade = :atividade, 
            grau_risco = :grau_risco, 
            atividade_principal = :atividade_principal
            WHERE id_local_atividade = :id_local_atividade
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':razao_social', trim($json['razao_social']));
            $stmt->bindParam(':nome_fantasia', trim($json['nome_fantasia']), isset($json['nome_fantasia']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':tipo_inscricao', trim($json['tipo_inscricao']));
            $stmt->bindParam(':nr_inscricao', trim($json['nr_inscricao']));
            $stmt->bindParam(':telefone', trim($json['telefone']), isset($json['telefone']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':email', trim($json['email']), isset($json['email']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':representante', trim($json['representante']), isset($json['representante']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':logradouro', trim($json['logradouro']), isset($json['logradouro']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':numero', trim($json['numero']), isset($json['numero']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':complemento', trim($json['complemento']), isset($json['complemento']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':bairro', trim($json['bairro']), isset($json['bairro']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':cidade', trim($json['cidade']), isset($json['cidade']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':uf', trim($json['uf']), isset($json['uf']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':cep', trim($json['cep']), isset($json['cep']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':id_local_atividade', trim($json['id']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Local de atividade atualizado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao atualizar o local de atividade!'
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
