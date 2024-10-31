<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['descricao']) && isset($json['grupo'])) {

            $sql = "
            INSERT INTO riscos (cod_esocial, descricao, grupo, cor, danos_saude) VALUES
            (:cod_esocial, :descricao, :grupo, :cor, :danos_saude)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cod_esocial', trim($json['cod_esocial']), trim($json['cod_esocial']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->bindParam(':grupo', trim($json['grupo']));
            $stmt->bindParam(':cor', trim($json['cor']), trim($json['cor']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':danos_saude', trim($json['danos_saude']), trim($json['danos_saude']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Risco cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar risco!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
