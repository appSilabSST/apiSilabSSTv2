<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['codigo']) && isset($json['atividade'])) {
            $sql = "
            INSERT INTO cnae (atividade, codigo) VALUES 
            (:atividade, :codigo)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':atividade', trim($json['atividade']));
            $stmt->bindParam(':codigo', trim($json['codigo']), PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {

                $id_cnae = $conn->lastInsertId();

                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Cnae criado com sucesso!',
                    'id' => $id_cnae
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o EPI!'
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
