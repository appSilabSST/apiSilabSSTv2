<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_rl_agendamento_exame']) && is_numeric($json['id_rl_agendamento_exame'])) {
            $sql = "
            UPDATE rl_agendamento_exames SET
            ";
            foreach ($json as $key => $value) {
                if ($key != 'id_rl_agendamento_exame') {
                    $sql .= "$key = :$key,";
                }
            }
            $sql = substr($sql, 0, -1) . "
            WHERE id_rl_agendamento_exame = :id_rl_agendamento_exame
            ";
            $stmt = $conn->prepare($sql);
            foreach ($json as $key => $value) {
                if ($key != 'id_rl_agendamento_exame') {

                    if ($key == 'id_resultado_exame' && $value == 0) {
                        $value = null;
                    } else  if ($key == 'valor' && $value == 0) {
                        $value = null;
                    }
                    $stmt->bindParam(":$key", trim($value), $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(":id_rl_agendamento_exame", $value);
                }
            }
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Exames atualizados com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao editar o exame!'
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
                'result' => 'Exames já existente neste agendamento!',
                'msg' => $th->getMessage()
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
} else {
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
