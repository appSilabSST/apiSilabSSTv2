<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $sql = "
            DELETE FROM regras_agendamento
            WHERE id_regra_agendamento = :id_regra_agendamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_regra_agendamento', trim($_GET['id']));
            $stmt->execute();

            http_response_code(200);
            $result = 'Regra de agendamento removida com sucesso!';
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // VÍNCULOS EM OUTRAS TABELAS
        if ($th->getCode() == 23000) {
            $result = 'Não é possível remover a regra de agendamento, pois há vínculos em outras tabelas';
        } else {
            $result = "$th->getCode() - $th->getMessage()";
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
