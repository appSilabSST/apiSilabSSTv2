<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_grupo_epi = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM grupos_epi
            WHERE ativo = '1'
            AND id_grupo_epi = :id_grupo_epi
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_grupo_epi', $id_grupo_epi);
        } else {
            $sql = "
            SELECT *
            FROM grupos_epi
            WHERE ativo = '1'
            ORDER BY grupo
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
