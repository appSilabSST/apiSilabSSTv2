<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id']) && isset($json['razao_social']) && isset($json['id_tipo_orgao']) && isset($json['nr_doc'])) {
            $id_empresa = $json['id'];

            $sql = "
            UPDATE empresas SET
            razao_social = :razao_social, 
            nome_fantasia = :nome_fantasia, 
            nr_doc_matriz = :nr_doc_matriz, 
            id_cnae = :id_cnae, 
            inscricao_estadual = :inscricao_estadual,
            cep = :cep, 
            logradouro = :logradouro, 
            numero = :numero, 
            complemento = :complemento, 
            bairro = :bairro, 
            cidade = :cidade, 
            uf = :uf, 
            ativo = :ativo
            WHERE id_empresa = :id_empresa
            ";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':razao_social', trim($json['razao_social']));
            $stmt->bindParam(':nome_fantasia', trim($json['nome_fantasia']), trim($json['nome_fantasia']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            // $stmt->bindParam(':id_tipo_orgao', trim($json['id_tipo_orgao']), trim($json['id_tipo_orgao']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            // $stmt->bindParam(':nr_doc', trim($json['nr_doc']));
            $stmt->bindParam(':nr_doc_matriz', trim($json['nr_doc_matriz']), trim($json['nr_doc_matriz']) === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':inscricao_estadual', trim($json['inscricao_estadual']),trim($json['inscricao_estadual']) === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_cnae', trim($json['id_cnae']), trim($json['id_cnae']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':cep', trim($json['cep']), trim($json['cep']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':logradouro', trim($json['logradouro']), trim($json['logradouro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':numero', trim($json['numero']), trim($json['numero']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':complemento', trim($json['complemento']), trim($json['complemento']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':bairro', trim($json['bairro']), trim($json['bairro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cidade', trim($json['cidade']), trim($json['cidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':uf', trim($json['uf']), trim($json['uf']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':ativo', trim($json['ativo']), PDO::PARAM_INT);
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
