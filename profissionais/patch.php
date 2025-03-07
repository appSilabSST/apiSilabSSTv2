<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_profissional']) && is_numeric($json['id_profissional'])) {

            $sql = "
            UPDATE profissionais SET
            ";
            foreach ($json as $key => $value) {
                if ($key != 'iid_profissionald') {
                    $sql .= "$key = :$key,";
                }
            }
            $sql = substr($sql, 0, -1) . "
            WHERE id_profissional = :id_profissional
            ";

            $stmt = $conn->prepare($sql);
            foreach ($json as $key => $value) {
                if ($key != 'id_profissional') {
                    $stmt->bindParam(":$key", trim($value), trim($value) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(":id_profissional", $value);
                }
            }
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Profissional atualizado com sucesso!'
            );
        } else {
            http_response_code(400);
            // DADOS ÚNICOS JÁ UTILIZADOS
            if ($th->getCode() == 23000) {
                $result = array(
                    'status' => 'fail',
                    'result' => 'CPF já existente!',
                    'error' => $th->getMessage()
                );
            } else {
                $result = array(
                    'status' => 'fail',
                    'result' => $th->getMessage()
                );
            }
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
