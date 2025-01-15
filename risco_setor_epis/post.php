<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_rl_setor_risco']) &&
            isset($json['id_epis_local_atividades'])
        ) {
            $sql = "
            INSERT INTO rl_risco_setor_epis (id_rl_setor_risco, id_epis_local_atividades) VALUES 
            (:id_rl_setor_risco, :id_epis_local_atividades)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_setor_risco', trim($json['id_rl_setor_risco']));
            $stmt->bindParam(':id_epis_local_atividades', trim($json['id_epis_local_atividades']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Vinculo EPI e Setor criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar Vinculo EPI e Setor!'
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
                'result' => 'Vinculo EPI e Setor já existente!'
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
