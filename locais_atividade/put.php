<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_empresa']) && is_numeric($json['id_empresa']) &&
            isset($json['id_tipo_ambiente']) && is_numeric($json['id_tipo_ambiente']) &&
            isset($json['atividade_principal'])
        ) {

            $sql = "
                    UPDATE locais_atividade SET
                        id_empresa = :id_empresa, 
                        razao_social = :razao_social, 
                        id_tipo_ambiente = :id_tipo_ambiente, 
                        id_cnae = :id_cnae,
                        grau_risco = :grau_risco,
                        nr_inscricao = :nr_inscricao,
                        id_tipo_orgao = :id_tipo_orgao,
                        atividade_principal = :atividade_principal,
                        logradouro = :logradouro,
                        numero = :numero,
                        complemento = :complemento,
                        cidade = :cidade,
                        cep = :cep,
                        uf = :uf
                    WHERE id_local_atividade = :id_local_atividade
            ";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':id_empresa', trim($json['id_empresa']));
            $stmt->bindValue(':razao_social', trim($json['razao_social']));
            $stmt->bindValue(':id_tipo_ambiente', trim($json['id_tipo_ambiente']));
            $stmt->bindParam(':id_cnae', trim($json['id_cnae']));
            $stmt->bindValue(':grau_risco', trim($json['grau_risco']));
            $stmt->bindValue(':nr_inscricao', trim($json['nr_inscricao']));
            $stmt->bindValue(':id_tipo_orgao', trim($json['id_tipo_orgao']));
            $stmt->bindValue(':atividade_principal', trim($json['atividade_principal']));
            $stmt->bindValue(':logradouro', isset($json['logradouro']) ? trim($json['logradouro']) : null, isset($json['logradouro']) && $json['logradouro'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':numero', isset($json['numero']) ? trim($json['numero']) : null, isset($json['numero']) && $json['numero'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':complemento', isset($json['complemento']) ? trim($json['complemento']) : null, isset($json['complemento']) && $json['complemento'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':cidade', isset($json['cidade']) ? trim($json['cidade']) : null, isset($json['cidade']) && $json['cidade'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':cep', isset($json['cep']) ? trim($json['cep']) : null, isset($json['cep']) && $json['cep'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':uf', isset($json['uf']) ? trim($json['uf']) : null, isset($json['uf']) && $json['uf'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':id_local_atividade', trim($json['id_local_atividade']));

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
