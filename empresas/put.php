<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id']) && isset($json['razao_social']) && isset($json['tipo_inscricao']) && isset($json['nr_inscricao'])) {
            $id_empresa = $json['id'];
            $sql = "
            UPDATE empresas SET
            razao_social = :razao_social, 
            nome_fantasia = :nome_fantasia, 
            tipo_inscricao = :tipo_inscricao, 
            nr_inscricao = :nr_inscricao, 
            telefone = :telefone, 
            data_cadastro = :data_cadastro, 
            cnae = :cnae, 
            atividade = :atividade, 
            grau_risco = :grau_risco, 
            cep = :cep, 
            logradouro = :logradouro, 
            numero = :numero, 
            complemento = :complemento, 
            bairro = :bairro, 
            cidade = :cidade, 
            uf = :uf, 
            status = :status
            WHERE id_empresa = :id_empresa
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
            $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Empresa atualizada com sucesso!'
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
