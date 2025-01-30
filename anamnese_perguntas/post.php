<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if ((isset($json["id_anamnese"]) && is_numeric($json["id_anamnese"])) &&  (isset($json["ordem"]) && is_numeric($json["ordem"]))  &&  isset($json["label"])) {

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
            http_response_code(204);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } catch (\Throwable $th) {
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            http_response_code(200);
            $result = array(
                'status' => 'fail',
                'result' => 'Ordem já selecionada por outra pergunta!',
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            );
        } else {
            http_response_code(500);
            $result = array(
                'status' => 'fail',
                'result' => $th->getMessage(),
                'code' => $th->getCode()
            );
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
