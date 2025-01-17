<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_agendamento = trim($_GET["id"]);
            $sql = "
            DELETE FROM agendamentos
            WHERE id_agendamento = :id_agendamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_agendamento', $id_agendamento);
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Agendamento removido com sucesso!'
            );
        } else if (isset($_GET["id_regra_agendamento"]) && is_numeric($_GET["id_regra_agendamento"])) {
            $id_regra_agendamento = trim($_GET["id_regra_agendamento"]);
            $sql = "
            DELETE FROM 
                agendamentos
            WHERE 
                id_regra_agendamento = :id_regra_agendamento
                AND nr_agendamento IS NULL
                AND id_pcmso IS NULL
                AND id_tipo_atendimento IS NULL
                AND id_local_atendimento = 0
                AND id_rl_colaborador_empresa IS NULL
                AND id_rl_setor_funcao IS NULL
                AND funcao IS NULL
                AND id_profissional IS NULL
                AND disponivel = 1
                AND cancelado = 0
                AND encaixe = 0
                AND justificativa_remarcacao IS NULL
                AND id_status_agendamento = 1
                AND observacao IS NULL
                AND corpo_documento IS NULL
                AND ativo = 1
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_regra_agendamento', $id_regra_agendamento);
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Agendamento removido com sucesso!'
            );
        }
    } catch (\Throwable $th) {
        http_response_code(200);
        $result = array(
            "status" => "fail",
            "error" => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
} else {
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
