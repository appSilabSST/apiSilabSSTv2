<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_agendamento']) && isset($json['exames']) && count($json['exames']) > 0) {
            // PREENCHE O AGENDAMENTO COM OS EXAMES ENVIADOS
            $sql = "
            INSERT INTO rl_agendamento_exames (id_agendamento,id_exame,data) VALUES
            ";
            foreach ($json['exames'] as $key => $value) {
                $sql .= "(" . trim($json['id_agendamento']) . ", :id_exame_$key, :data_exame_$key
                ),";
            }
            $sql = substr($sql, 0, -1);
            $stmt = $conn->prepare($sql);

            foreach ($json['exames'] as $key => $value) {
                $stmt->bindParam(":id_exame_$key", trim($value['id_exame']));
                $stmt->bindParam(":data_exame_$key", trim($value['data']));
            }
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Exames cadastrados com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar os exames!'
                );
            }
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } catch (\Throwable $th) {
        $conn->rollBack();
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Exame já existente neste agendamento!',
                'error' => $th->getMessage()
            );
        } else {
            $result = array(
                'status' => 'fail',
                'result' => $th->getMessage()
            );
        }
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
