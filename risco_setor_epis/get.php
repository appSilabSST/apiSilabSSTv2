<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_epis_local_atividades = trim($_GET["id"]);
            $sql = "
            SELECT ela.*
            FROM epis_local_atividades ela
            JOIN locais_atividade l ON (l.id_local_atividade = ela.id_local_atividade)
            JOIN grupos_epi ge ON (ge.id_grupo_epi = ela.id_grupo_epi)
            WHERE ela.ativo = '1'
            AND ela.id_epis_local_atividades = :id_epis_local_atividades
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_epis_local_atividades', $id_epis_local_atividades);
        } elseif (isset($_GET["id_local_atividade"]) && is_numeric($_GET["id_local_atividade"])) {
            $id_local_atividade = trim($_GET["id_local_atividade"]);
            $sql = "
            SELECT ela.*,ge.grupo
            FROM epis_local_atividades ela
            JOIN grupos_epi ge ON (ge.id_grupo_epi = ela.id_grupo_epi)
            WHERE ela.ativo = '1'
            AND ela.id_local_atividade = :id_local_atividade
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_local_atividade', $id_local_atividade);
        } else {
            $sql = "
            SELECT ela.*
            FROM epis_local_atividades ela
            JOIN locais_atividade l ON (l.id_local_atividade = ela.id_local_atividade)
            JOIN grupos_epi ge ON (ge.id_grupo_epi = ela.id_grupo_epi)
            WHERE ela.ativo = '1'
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
