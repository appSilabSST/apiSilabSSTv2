<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE colaboradores SET
            nome = :nome,
            nome_social = :nome_social,
            celular = :celular,
            email = :email,
            tipo_doc = :tipo_doc,
            nr_doc = :nr_doc,
            rg = :rg,
            deficiente = :deficiente,
            data_nascimento = :data_nascimento,
            sexo = :sexo
            WHERE id_colaborador = :id_colaborador
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':nome_social', trim($json['nome_social']));
            $stmt->bindParam(':celular', trim($json['celular']));
            $stmt->bindParam(':email', trim($json['email']));
            $stmt->bindParam(':tipo_doc', trim($json['tipo_doc']));
            $stmt->bindParam(':nr_doc', trim($json['nr_doc']));
            $stmt->bindParam(':rg', trim($json['rg']), trim($json['rg']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':deficiente', trim($json['deficiente']));
            $stmt->bindParam(':data_nascimento', trim($json['data_nascimento']));
            $stmt->bindParam(':sexo', trim($json['sexo']));
            $stmt->bindParam(':id_colaborador', trim($json['id']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Colaborador atualizado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao atualizar o colaborador!'
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
                'result' => 'RG, CPF ou Passaporte já existente!'
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
