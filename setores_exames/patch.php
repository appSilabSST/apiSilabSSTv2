<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_rl_setor_funcao']) && is_numeric($json['id_rl_setor_funcao'])) {

            $sql = "
            UPDATE rl_setores_exames SET
            ";
            foreach ($json as $key => $value) {
                if ($key != 'id_rl_setor_funcao') {
                    $sql .= "$key = :$key,";
                }
            }
            $sql = substr($sql, 0, -1) . ",
            data_edit = NOW()
            WHERE id_rl_setor_exame = :id_rl_setor_exame
            ";

            $stmt = $conn->prepare($sql);
            foreach ($json as $key => $value) {
                if ($key != 'id_rl_setor_funcao') {
                    $stmt->bindParam(":$key", trim($value), trim($value) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(":id_rl_setor_exame", $value);
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
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Exame já existente!',
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
