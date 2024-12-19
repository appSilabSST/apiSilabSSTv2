<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_risco_exame = trim($_GET["id"]);
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , e.procedimento , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format
            FROM rl_riscos_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            WHERE rl.ativo = 1
            AND rl.id_rl_risco_exame = :id_rl_risco_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_risco_exame', $id_rl_risco_exame);
        } elseif (isset($_GET["id_risco"]) && is_numeric($_GET["id_risco"])) {
            $id_risco = trim($_GET["id_risco"]);
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , e.procedimento , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format
            FROM rl_riscos_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            WHERE rl.ativo = 1
            AND rl.id_risco = :id_risco
            ORDER BY e.procedimento
            ";
            $sql = "
                SELECT 
                    rl.*,
                    e.procedimento, 
                    e.cod_esocial, 
                    CONCAT_WS(' | eSocial: ', e.procedimento, e.cod_esocial) AS procedimento_format,
                    
                    -- Lógica para padronizar_mask
                    CASE
                        WHEN rl.padronizar = 1 THEN '<div class=\"alert mb-0 alert-success text-center\" role=\"alert\">Ativo</div>'
                        ELSE '<div class=\"alert mb-0 alert-danger text-center\" role=\"alert\">Inativo</div>'
                    END AS padronizar_mask,

                    -- Lógica para tipos_avaliacao
                    CONCAT(
                        CASE WHEN rl.admissional = 1 THEN 'Admissional, ' ELSE '' END,
                        CASE WHEN rl.periodico = 1 THEN 'Periódico, ' ELSE '' END,
                        CASE WHEN rl.mudanca_risco = 1 THEN 'Mudança de Risco, ' ELSE '' END,
                        CASE WHEN rl.retorno_trabalho = 1 THEN 'Retorno ao Trabalho, ' ELSE '' END,
                        CASE WHEN rl.demissional = 1 THEN 'Demissional, ' ELSE '' END
                    ) AS tipos_avaliacao,

                    -- Lógica para periodicidade_format
                    CASE
                        WHEN rl.periodicidade = 0 THEN '<div class=\"text-center\"> - </div>'
                        WHEN rl.periodicidade = 1 THEN '<div class=\"text-center\"> 1 mês </div>'
                        ELSE CONCAT('<div class=\"text-center\"> ', rl.periodicidade, ' meses </div>')
                    END AS periodicidade_format

                FROM rl_riscos_exames AS rl
                JOIN exames e ON (rl.id_exame = e.id_exame)
                WHERE rl.ativo = '1'
                AND rl.id_risco = :id_risco
                ORDER BY e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_risco', $id_risco);
        } else {
            $sql = "
            SELECT rl.*,
            e.procedimento , e.cod_esocial , e.procedimento , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod_esocial) procedimento_format
            FROM rl_riscos_exames AS rl
            JOIN exames e ON (rl.id_exame = e.id_exame)
            WHERE rl.ativo = 1
            ORDER BY e.procedimento
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
