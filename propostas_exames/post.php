<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_proposta']) && is_numeric($json['id_proposta']) &&
            isset($json['id_exame']) && is_numeric($json['id_exame']) &&
            isset($json['valor']) && is_numeric($json['valor'])
        ) {
            $sql = "
            INSERT INTO rl_propostas_exames (id_proposta , id_exame , valor) VALUES 
            (:id_proposta , :id_exame , :valor)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_proposta', trim($json['id_proposta']));
            $stmt->bindParam(':id_exame', trim($json['id_exame']));
            $stmt->bindParam(':valor', trim($json['valor']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Exame cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar exame!'
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
                'result' => 'Exame já existente nessa proposta!'
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
