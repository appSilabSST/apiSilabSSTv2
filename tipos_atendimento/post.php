<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['tipo_atendimento'])) {

            $sql = "
            INSERT INTO tipos_atendimento (cod_esocial, tipos_atendimento) VALUES
            (:cod_esocial, :tipos_atendimento)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cod_esocial', trim($json['cod_esocial']), trim($json['cod_esocial']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':tipos_atendimento', trim($json['tipos_atendimento']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Tipo de atendimento cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar tipo de atendimento!'
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
