<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['nome']) && isset($json['id_empresa']) && is_numeric($json['id_empresa'])) {
            $sql = "
            INSERT INTO contatos_empresas (id_empresa, nome, funcao, telefone, email) VALUES 
            (:id_empresa, :nome, :funcao, :telefone, :email)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':funcao', trim($json['funcao']), trim($json['funcao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':telefone', trim($json['telefone']), trim($json['telefone']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':email', trim($json['email']), trim($json['email']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Contato da empresa criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o contato da empresa!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
