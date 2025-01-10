<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['razao_social']) && isset($json['id_tipo_orgao']) && isset($json['nr_doc'])) {

            $sql = "
            INSERT INTO empresas (razao_social, nome_fantasia, id_tipo_orgao, nr_doc, nr_doc_matriz,inscricao_estadual, data_cadastro, id_cnae, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES
            (:razao_social, :nome_fantasia, :id_tipo_orgao, :nr_doc, :nr_doc_matriz, :inscricao_estadual,CURRENT_DATE(), :id_cnae, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':razao_social', trim($json['razao_social']));
            $stmt->bindParam(':nome_fantasia', trim($json['nome_fantasia']), trim($json['nome_fantasia']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_tipo_orgao', trim($json['id_tipo_orgao']), trim($json['id_tipo_orgao']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':nr_doc', trim($json['nr_doc']));
            $stmt->bindParam(':nr_doc_matriz', trim($json['nr_doc_matriz']), trim($json['nr_doc_matriz']) === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':inscricao_estadual', trim($json['inscricao_estadual']), trim($json['inscricao_estadual']) === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_cnae', trim($json['id_cnae']), trim($json['id_cnae']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':cep', trim($json['cep']), trim($json['cep']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':logradouro', trim($json['logradouro']), trim($json['logradouro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':numero', trim($json['numero']), trim($json['numero']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':complemento', trim($json['complemento']), trim($json['complemento']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':bairro', trim($json['bairro']), trim($json['bairro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cidade', trim($json['cidade']), trim($json['cidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':uf', trim($json['uf']), trim($json['uf']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $id_empresa = $conn->lastInsertId();

                // Create both cURL resources
                $ch = curl_init();


                // CHAMA A API PARA CADASTRAR OS LOCAL DE ATIVIDADE DO PRÓPRIO EMPREGADOR AO CRIADO A EMPRESA
                $postfields = array(
                    'id_empresa' => $id_empresa,
                    'id_empresa_local_atividade' => $id_empresa,
                    'id_tipo_ambiente' => 1
                );
                curl_setopt_array($ch, array(
                    CURLOPT_URL => "https://silabsst.com.br/_backend/locais_atividade/",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($postfields),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: $token",
                        "Content-Type: application/json"
                    ),
                ));

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Empresa cadastrada  e Local de atividade com sucesso!',
                    'result2' =>  $response
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
