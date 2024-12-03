<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_setor_risco']) && is_numeric($json['id_setor_risco']) &&
            isset($json['id_epi']) && is_numeric($json['id_epi'])
        ) {

            $sql = "
            INSERT INTO rl_riscos_epis (id_setor_risco, id_epi, ca, data_new) VALUES
            (:id_setor_risco, :id_epi, :ca, NOW())
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_setor_risco', trim($json['id_setor_risco']));
            $stmt->bindParam(':id_epi', trim($json['id_epi']));
            $stmt->bindParam(':ca', trim($json['ca']), isset($json['ca']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'EPI cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar EPI!'
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
                'result' => 'EPI já existente!',
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
