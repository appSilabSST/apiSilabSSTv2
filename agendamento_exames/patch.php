<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE rl_agendamento_exames SET
            ";
            foreach ($json as $key => $value) {
                if ($key != 'id') {
                    $sql .= "$key = :$key,";
                } elseif ($key == 'id' && count($json) == 1) {
                    // PROCURA SE POSSUI EXAME PARA REAPROVEITAMENTO
                    $sql_ = "
                    SELECT (
                        SELECT id_rl_agendamento_exame
                        FROM rl_agendamento_exames
                        WHERE id_exame = rl.id_exame
                        AND id_agendamento <> rl.id_agendamento
                        AND realizado = 1
                        AND id_agendamento IN (
                            SELECT id_agendamento
                            FROM agendamentos
                            WHERE id_rl_colaborador_empresa = (
                                SELECT id_rl_colaborador_empresa
                                FROM agendamentos
                                WHERE id_agendamento = rl.id_agendamento
                            )
                        )
                        AND rl.data <= (
                            SELECT DATE_ADD(rl_agendamento_exames.data, INTERVAL validade MONTH)
                            FROM exames
                            WHERE id_exame = rl.id_exame
                        )
                        ORDER BY rl_agendamento_exames.data DESC
                        LIMIT 1
                    ) id_aproveitado FROM rl_agendamento_exames rl
                    WHERE rl.id_rl_agendamento_exame = :id_rl_agendamento_exame
                    ";
                    // echo $sql_;exit;
                    $stmt = $conn->prepare($sql_);
                    $stmt->bindValue(":id_rl_agendamento_exame", $value);
                    $stmt->execute();

                    $dados = $stmt->fetch(PDO::FETCH_OBJ);
                    $id_aproveitado = $dados->id_aproveitado ?? 0;

                    $sql .= "
                    id_reaproveitado = IF(id_reaproveitado > 0, NULL, IF($id_aproveitado = 0, NULL, $id_aproveitado))
                    ";
                } else {
                }
            }
            $sql = substr($sql, 0, -1) . "
            WHERE id_rl_agendamento_exame = :id_rl_agendamento_exame
            ";
            // echo $sql;exit;
            $stmt = $conn->prepare($sql);
            foreach ($json as $key => $value) {
                if ($key != 'id') {
                    $stmt->bindParam(":$key", trim($value), trim($value) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(":id_rl_agendamento_exame", $value);
                }
            }

            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Exames atualizados com sucesso!'
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
                'result' => 'Exame já existente neste agendamento!'
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
