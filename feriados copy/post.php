<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_profissional']) && is_numeric($json['id_profissional']) &&
            isset($json['data'])
        ) {

            $sql = "
                INSERT INTO escalas_profissionais 
                    (id_profissional, data) 
                VALUES 
                    (:id_profissional, :data)
            ";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':data', trim($json['data']));
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']), PDO::PARAM_INT);

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
