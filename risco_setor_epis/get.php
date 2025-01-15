<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_risco_setor_epis = trim($_GET["id"]);
            $sql = "
            SELECT rse.*
            FROM rl_risco_setor_epis rse
            WHERE rse.id_rl_risco_setor_epis = :id_rl_risco_setor_epis
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_risco_setor_epis', $id_rl_risco_setor_epis);
        } elseif (isset($_GET["id_epis_local_atividades"]) && is_numeric($_GET["id_epis_local_atividades"])) {
            $id_epis_local_atividades = trim($_GET["id_epis_local_atividades"]);
            $sql = "
            SELECT rse.*
            FROM rl_risco_setor_epis rse
            JOIN rl_setores_riscos sr ON (sr.id_rl_setor_risco = rse.id_rl_setor_risco)
            WHERE rse.id_epis_local_atividades = :id_epis_local_atividades
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_epis_local_atividades', $id_epis_local_atividades);
        } else {
            $sql = "
                SELECT rse.*
                FROM rl_risco_setor_epis rse
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
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
