<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_empresa']) && is_numeric($json['id_empresa']) && isset($json['id_tipo_ambiente']) && is_numeric($json['id_tipo_ambiente'])) {
            $sql = "
            INSERT INTO locais_atividade 
                (id_empresa,razao_social, id_tipo_ambiente,id_cnae,grau_risco,nr_inscricao,id_tipo_orgao,atividade_principal,logradouro,numero,complemento,bairro,cidade,cep,uf) 
            VALUES 
                (:id_empresa,:razao_social, :id_tipo_ambiente, :id_cnae,:grau_risco,:nr_inscricao,:id_tipo_orgao,:atividade_principal,:logradouro,:numero,:complemento,:bairro,:cidade,:cep,:uf)
            ";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':razao_social', trim($json['razao_social']));
            $stmt->bindParam(':id_tipo_ambiente', trim($json['id_tipo_ambiente']));
            $stmt->bindParam(':id_cnae', trim($json['id_cnae']));
            $stmt->bindParam(':grau_risco', trim($json['grau_risco']));
            $stmt->bindParam(':nr_inscricao', trim($json['nr_inscricao']));
            $stmt->bindParam(':id_tipo_orgao', trim($json['id_tipo_orgao']));
            $stmt->bindParam(':atividade_principal', trim($json['atividade_principal']));
            $stmt->bindParam(':logradouro', trim($json['logradouro']), trim($json['logradouro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':numero', trim($json['numero']), trim($json['numero']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':complemento', trim($json['complemento']), trim($json['complemento']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':bairro', trim($json['bairro']), trim($json['bairro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cidade', trim($json['cidade']), trim($json['cidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cep', trim($json['cep']), trim($json['cep']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':uf', trim($json['uf']), trim($json['uf']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            
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
