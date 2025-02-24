<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_exame']) && is_numeric($json['id_exame'])) {

            $sql = "
            UPDATE exames SET
            ";
            foreach ($json as $key => $value) {
                if ($key != 'id_exame') {
                    $sql .= "$key = :$key,";
                }
            }
            $sql = substr($sql, 0, -1) . "
            WHERE id_exame = :id_exame
            ";

            $stmt = $conn->prepare($sql);
            foreach ($json as $key => $value) {
                if ($key != 'id_exame') {
                    $stmt->bindParam(":$key", trim($value), trim($value) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(":id_exame", $value);
                }
            }
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Exame atualizado com sucesso!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
