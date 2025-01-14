<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_epis_local_atividades'])) {
            $sql = "
            UPDATE epis_local_atividades SET
            ";
            foreach ($json as $key => $value) {
                if ($key != 'id_epis_local_atividades') {
                    $sql .= "$key = :$key,";
                }
            }
            $sql = substr($sql, 0, -1) . "
            WHERE id_epis_local_atividades = :id_epis_local_atividades
            ";

            $stmt = $conn->prepare($sql);
            foreach ($json as $key => $value) {
                if ($key != 'id_epis_local_atividades') {
                    $stmt->bindParam(":$key", trim($value), trim($value) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(":id_epis_local_atividades", $value);
                }
            }
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Epis Local de atividade atualizado com sucesso!'
            );
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
                'result' => 'C.A do epis Local de atividade já existente!'
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
