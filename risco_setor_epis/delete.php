<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $sql = "
            DELETE FROM rl_risco_setor_epis
            WHERE id_epis_local_atividades = :id_epis_local_atividades
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_epis_local_atividades', trim($_GET['id']));
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Vinculo EPI e Setor removido com sucesso!',
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage(),
            'code' => $th->getCode()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
