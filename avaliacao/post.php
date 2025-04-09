<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_rl_agendamento_exame']) && is_numeric($json['id_rl_agendamento_exame']) &&
            isset($json['resultado']) && is_numeric($json['resultado'])
        ) {
            // Prepara os dados com condições na mesma linha
            $avaliacao = empty($json['avaliacao']) || $json['avaliacao'] === '{}' ? null : json_encode($json['avaliacao']);
            $anamnese = empty($json['anamnese']) || $json['anamnese'] === '{}' ? null : json_encode($json['anamnese']);
            $anotacao = isset($json['anotacao']) && $json['anotacao'] !== null ? trim($json['anotacao']) : null;

            // Prepara o SQL para inserir a avaliação
            $sql = "
            INSERT INTO avaliacao (id_rl_agendamento_exame, avaliacao, resultado, id_profissional,anotacao,anamnese) 
            VALUES (:id_rl_agendamento_exame, :avaliacao, :resultado,:id_profissional, :anotacao,:anamnese)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_agendamento_exame', trim($json['id_rl_agendamento_exame']), PDO::PARAM_INT);
            $stmt->bindParam(':resultado', trim($json['resultado']), PDO::PARAM_INT);
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']), PDO::PARAM_INT);
            $stmt->bindValue(':anamnese', $anamnese, $anamnese === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':avaliacao', $avaliacao, $avaliacao === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':anotacao', $anotacao, $anotacao === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Avaliação registrada com sucesso!'
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
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Avaliação já existente!',
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
}
