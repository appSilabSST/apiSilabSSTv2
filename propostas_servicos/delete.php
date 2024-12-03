<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $sql = "
            DELETE FROM rl_propostas_servicos
            WHERE id_rl_proposta_servico = :id_rl_proposta_servico
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_proposta_servico', trim($_GET['id']));
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Serviço removido com sucesso!'
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
                'result' => 'Não é possível remover o serviço, pois há vínculos em outras tabelas'
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
