<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['razao_social']) && isset($json['tipo_inscricao']) && isset($json['nr_inscricao'])) {

            $sql = "
            INSERT INTO empresas (razao_social, nome_fantasia, tipo_inscricao, nr_inscricao, telefone, data_cadastro, cnae, atividade, grau_risco, cep, logradouro, numero, complemento, bairro, cidade, uf, status) VALUES
            (:razao_social, :nome_fantasia, :tipo_inscricao, :nr_inscricao, :telefone, :data_cadastro, :cnae, :atividade, :grau_risco, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf, :status)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':razao_social', trim($json['razao_social']));
            $stmt->bindParam(':nome_fantasia', trim($json['nome_fantasia']), trim($json['nome_fantasia']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':tipo_inscricao', trim($json['tipo_inscricao']));
            $stmt->bindParam(':nr_inscricao', trim($json['nr_inscricao']));
            $stmt->bindParam(':telefone', trim($json['telefone']), trim($json['telefone']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':data_cadastro', trim($json['data_cadastro']), trim($json['data_cadastro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cnae', trim($json['cnae']), trim($json['cnae']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':atividade', trim($json['atividade']), trim($json['atividade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':grau_risco', trim($json['grau_risco']), trim($json['grau_risco']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cep', trim($json['cep']), trim($json['cep']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':logradouro', trim($json['logradouro']), trim($json['logradouro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':numero', trim($json['numero']), trim($json['numero']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':complemento', trim($json['complemento']), trim($json['complemento']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':bairro', trim($json['bairro']), trim($json['bairro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cidade', trim($json['cidade']), trim($json['cidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':uf', trim($json['uf']), trim($json['uf']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':status', trim($json['status']), PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Empresa cadastrada com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar empresa!'
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
                'result' => 'Empresa já existente!'
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
