<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET['id_rl_proposta_exame']) && is_numeric($_GET['id_rl_proposta_exame'])) {
            $sql = "
            DELETE FROM rl_propostas_exames
            WHERE id_rl_proposta_exame = :id_rl_proposta_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_proposta_exame', trim($_GET['id_rl_proposta_exame']));
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Exame removido com sucesso!'
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
        // VÍNCULOS EM OUTRAS TABELAS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Não é possível remover o exame, pois há vínculos em outras tabelas'
            );
        } else {
            $result = array(
                'status' => 'fail',
                'result' => $th->getMessage(),
                'code' => $th->getCode()
            );
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
