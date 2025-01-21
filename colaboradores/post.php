<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['nome']) && isset($json['sexo']) && isset($json['data_nascimento']) && isset($json['tipo_doc']) && isset($json['nr_doc'])) {
            $sql = "
            INSERT INTO colaboradores (nome,nome_social,celular,email,id_tipo_orgao,nr_doc,rg,deficiente,data_nascimento,sexo) VALUES 
            (:nome,:nome_social,:celular,:email,:id_tipo_orgao,:nr_doc,:rg,:deficiente,:data_nascimento,:sexo)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':nome_social', trim($json['nome_social']));
            $stmt->bindParam(':celular', trim($json['celular']));
            $stmt->bindParam(':email', trim($json['email']));
            $stmt->bindParam(':id_tipo_orgao', trim($json['id_tipo_orgao']));
            $stmt->bindParam(':nr_doc', trim($json['nr_doc']));
            $stmt->bindParam(':rg', trim($json['rg']), trim($json['rg']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':deficiente', trim($json['deficiente']));
            $stmt->bindParam(':data_nascimento', trim($json['data_nascimento']));
            $stmt->bindParam(':sexo', trim($json['sexo']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Colaborador criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o colaborador!'
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
                'result' => 'RG, CPF ou Passaporte já existente!',
                'error' => $th->getMessage()
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
