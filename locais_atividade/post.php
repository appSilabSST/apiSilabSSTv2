<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['razao_social']) && isset($json['tipo_inscricao']) && isset($json['nr_inscricao'])) {
            $sql = "
            INSERT INTO locais_atividade (id_empresa, id_tipo_ambiente, razao_social, tipo_inscricao, nr_inscricao, logradouro, numero, complemento, bairro, cidade, uf, cep, cnae, atividade, grau_risco, atividade_principal) VALUES 
            (:id_empresa, :id_tipo_ambiente, :razao_social, :tipo_inscricao, :nr_inscricao, :logradouro, :numero, :complemento, :bairro, :cidade, :uf, :cep, :cnae, :atividade, :grau_risco, :atividade_principal)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_tipo_ambiente', trim($json['id_tipo_ambiente']));
            $stmt->bindParam(':razao_social', trim($json['razao_social']));
            $stmt->bindParam(':tipo_inscricao', trim($json['tipo_inscricao']));
            $stmt->bindParam(':nr_inscricao', trim($json['nr_inscricao']));
            $stmt->bindParam(':logradouro', trim($json['logradouro']), isset($json['logradouro']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':numero', trim($json['numero']), isset($json['numero']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':complemento', trim($json['complemento']), isset($json['complemento']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':bairro', trim($json['bairro']), isset($json['bairro']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':cidade', trim($json['cidade']), isset($json['cidade']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':uf', trim($json['uf']), isset($json['uf']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':cep', trim($json['cep']), isset($json['cep']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':cnae', trim($json['cnae']), isset($json['cnae']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':atividade', trim($json['atividade']), isset($json['atividade']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':grau_risco', trim($json['grau_risco']), isset($json['grau_risco']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':atividade_principal', trim($json['atividade_principal']), isset($json['atividade_principal']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
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
