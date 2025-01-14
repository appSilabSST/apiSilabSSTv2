<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {

            $sql = "
            UPDATE rl_setores_riscos_planos_acao SET
            ";
            foreach ($json as $key => $value) {
                if ($key != 'id_rl_setor_risco_plano_acao') {
                    $sql .= "$key = :$key,";
                }
            }
            $sql = substr($sql, 0, -1) . ",
            data_edit = NOW()
            WHERE id_rl_setor_risco_plano_acao = :id_rl_setor_risco_plano_acao
            ";

            $stmt = $conn->prepare($sql);
            foreach ($json as $key => $value) {
                if ($key != 'id_rl_setor_risco_plano_acao') {
                    $stmt->bindParam(":$key", trim($value), trim($value) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                } else {
                    $stmt->bindValue(":id_rl_setor_risco_plano_acao", $value);
                }
            }
            $stmt->execute();

            http_response_code(200);
            $result = 'Plano de ação atualizado com sucesso!';
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = 'Plano de ação já existente!';
        } else {
            $result = $th->getMessage();
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
