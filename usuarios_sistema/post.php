<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_permissao']) && is_numeric($json['id_permissao']) &&
            isset($json['username']) && isset($json['senha'])
        ) {

            $sql = "
                INSERT INTO usuarios_sistema 
                    (id_permissao,nome, username,senha)
                VALUES 
                    (:id_permissao,:nome,:username,:senha) 
            ";

            $senha = hash('sha256', trim($postjson['senha']));

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':nome', trim($json['nome']), PDO::PARAM_STR);
            $stmt->bindParam(':id_permissao', trim($json['id_permissao']), PDO::PARAM_INT);
            $stmt->bindParam(':username', trim($json['username']), PDO::PARAM_STR);
            $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Vinculo criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o Vinculo!'
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
                'result' => 'Cnae já existente!',
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
