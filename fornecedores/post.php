<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['razao_social']) && isset($json['nr_doc'])) {
            $sql = "
            INSERT INTO fornecedores 
                (razao_social, nome_fantasia, id_tipo_orgao, nr_doc,responsavel,responsavel_email,responsavel_cpf,  celular, logradouro, numero, complemento, bairro, cidade, uf, cep) 
            VALUES 
                (:razao_social, :nome_fantasia, :id_tipo_orgao, :nr_doc, :responsavel, :responsavel_email,:responsavel_cpf,  :celular,  :logradouro, :numero, :complemento, :bairro, :cidade, :uf, :cep)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nr_doc', trim($json['nr_doc']));
            $stmt->bindParam(':id_tipo_orgao', trim($json['id_tipo_orgao']));
            $stmt->bindParam(':razao_social', trim($json['razao_social']));
            $stmt->bindParam(':nome_fantasia', trim($json['nome_fantasia']), isset($json['nome_fantasia']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel', trim($json['responsavel']), isset($json['responsavel']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel_email', trim($json['responsavel_email']), isset($json['responsavel_email']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':responsavel_cpf', trim($json['responsavel_cpf']), isset($json['responsavel_cpf']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':celular', trim($json['celular']), isset($json['celular']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':logradouro', trim($json['logradouro']), isset($json['logradouro']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':numero', trim($json['numero']), isset($json['numero']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':bairro', trim($json['bairro']), isset($json['bairro']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':cidade', trim($json['cidade']), isset($json['cidade']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':uf', trim($json['uf']), isset($json['uf']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':complemento', trim($json['complemento']), isset($json['complemento']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':cep', trim($json['cep']), isset($json['cep']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Fornecedor criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o fornecedor!'
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
                'result' => 'Fornecedor já existente!',
                'getMessage' => $th->getMessage()
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
