<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["df"])) {
            $data_inicio = isset($_GET["di"]) && $_GET["di"] <> "undefined" ? trim($_GET['di']) : '1900-01-01';
            $data_fim = trim($_GET["df"]);
            $sql = "
            SELECT *, (valor_exames - valor_desconto) debito, (valor_exames - valor_custo) lucratividade
            FROM (
                SELECT e.id_empresa,e.razao_social,
                CONCAT(COUNT(rl1.id_agendamento), ' A - ', COUNT(rl1.id_exame), ' E') agendamentos_exames, SUM(rl1.valor) valor_exames,
                IFNULL(ex.valor_desconto, 0) valor_desconto, IFNULL(ex.valor_custo, 0) valor_custo
                FROM rl_agendamento_exames rl1
                JOIN exames ex ON rl1.id_exame = ex.id_exame
                JOIN agendamentos a ON rl1.id_agendamento = a.id_agendamento
                JOIN rl_colaboradores_empresas rl2 ON a.id_rl_colaborador_empresa = rl2.id_rl_colaborador_empresa
                JOIN empresas e ON rl2.id_empresa = e.id_empresa
                WHERE (rl1.`data` BETWEEN :data_inicio AND :data_fim)
                AND rl1.realizado = 1
                AND rl1.cobrar = 1
                GROUP BY e.id_empresa
            ) AS r
            ORDER BY valor_exames DESC
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data_inicio', $data_inicio);
            $stmt->bindParam(':data_fim', $data_fim);
            // EXECUTAR SINTAXE SQL
            $stmt->execute();

            $result = getResult($stmt);
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
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
