<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_anamnese']) &&
            is_numeric($json['id_anamnese']) &&
            isset($json['perguntas']) &&
            count($json['perguntas']) > 0 &&
            !in_array(null, $json['perguntas'])
        ) {

            // Inicia a transação
            $conn->beginTransaction();

            // PREENCHE O AGENDAMENTO COM OS EXAMES ENVIADOS
            $sql = "INSERT INTO anamnese_perguntas (label, ordem, id_anamnese) VALUES ";

            $values = [];
            foreach ($json['perguntas'] as $key => $value) {
                $values[] = "(:label_$key, :ordem_$key, :id_anamnese)";
            }

            $sql .= implode(", ", $values);

            // Prepara a consulta
            $stmt = $conn->prepare($sql);

            // Vincula os parâmetros
            foreach ($json['perguntas'] as $key => $value) {
                $stmt->bindParam(":label_$key", $value['label']);
                $stmt->bindParam(":ordem_$key", $value['ordem']);
            }

            $stmt->bindParam(":id_anamnese", $json['id_anamnese']);

            // Executa a query
            $stmt->execute();

            // Verifica o sucesso da operação
            if ($stmt->rowCount() > 0) {
                $conn->commit(); // Confirma a transação
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Exames cadastrados com sucesso!'
                );
            } else {
                $conn->rollBack(); // Reverte a transação
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar os exames!'
                );
            }
        } else if ((isset($json["id_anamnese"]) && is_numeric($json["id_anamnese"])) &&  (isset($json["ordem"]) && is_numeric($json["ordem"]))  &&  isset($json["label"])) {

            $sql = "INSERT INTO anamnese_perguntas (label,ordem,id_anamnese) VALUES (:label,:ordem,:id_anamnese)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese', trim($json['id_anamnese']), PDO::PARAM_INT);
            $stmt->bindParam(':ordem', trim($json['ordem']));
            $stmt->bindParam(':label', trim($json['label']));

            $stmt->execute();

            http_response_code(201);
            $result = array(
                'status' => 'success',
                'result' => 'Criado com sucesso!'
            );
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
