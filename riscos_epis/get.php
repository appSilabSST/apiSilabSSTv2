<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_risco_epi = trim($_GET["id"]);
            $sql = "
            SELECT rl.id_rl_risco_epi , rl.id_rl_setor_risco , rl.id_epi , rl.ca ,
            e.grupo , e.epi , CONCAT_WS(' - ' , e.grupo , e.epi) grupo_epi
            FROM rl_riscos_epis rl
            JOIN rl_setores_riscos rl2 ON (rl.id_rl_setor_risco = rl2.id_rl_setor_risco)
            JOIN epis e ON (rl.id_epi = e.id_epi)
            WHERE rl.ativo = 1
            AND id_rl_risco_epi = :id_rl_risco_epi
            ORDER BY e.grupo , e.epi
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_risco_epi', $id_rl_risco_epi);
        } elseif (isset($_GET["id_rl_setor_risco"]) && is_numeric($_GET["id_rl_setor_risco"])) {
            $id_rl_setor_risco = trim($_GET["id_rl_setor_risco"]);
            $sql = "
            SELECT rl.id_rl_risco_epi , rl.id_rl_setor_risco , rl.id_epi , rl.ca ,
            e.grupo , e.epi , CONCAT_WS(' - ' , e.grupo , e.epi) grupo_epi
            FROM rl_riscos_epis rl
            JOIN rl_setores_riscos rl2 ON (rl.id_rl_setor_risco = rl2.id_rl_setor_risco)
            JOIN epis e ON (rl.id_epi = e.id_epi)
            WHERE rl.ativo = 1
            AND rl.id_rl_setor_risco = :id_rl_setor_risco
            ORDER BY e.grupo , e.epi
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_setor_risco', $id_rl_setor_risco);
        } else {
            $sql = "
            SELECT rl.id_rl_risco_epi , rl.id_rl_setor_risco , rl.id_epi , rl.ca ,
            e.grupo , e.epi , CONCAT_WS(' - ' , e.grupo , e.epi) grupo_epi
            FROM rl_riscos_epis rl
            JOIN rl_setores_riscos rl2 ON (rl.id_rl_setor_risco = rl2.id_rl_setor_risco)
            JOIN epis e ON (rl.id_epi = e.id_epi)
            WHERE rl.ativo = 1
            ORDER BY e.grupo , e.epi
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
