<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_empresa']) && isset($json['id_empresa']) &&
            isset($json['id_cnae']) && isset($json['id_cnae']) &&
            isset($json['classe']) && isset($json['classe'])
        ) {

            // Caso o tipo da classe seja 1, altere todos os outros cnae da empresa classe 2
            if ($json['classe'] == 1) {
                $sql = "UPDATE rl_empresa_cnae SET classe = 2 WHERE id_empresa = :id_empresa";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_empresa', $json['id_empresa']);
                $stmt->execute();
            }

            $sql = "
                INSERT INTO rl_empresa_cnae 
                    (id_empresa, id_cnae,classe) 
                VALUES 
                    (:id_empresa, :id_cnae,:classe)
            ";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_cnae', trim($json['id_cnae']), PDO::PARAM_INT);
            $stmt->bindParam(':classe', trim($json['classe']), PDO::PARAM_INT);

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
