<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_anamnese_pergunta']) && is_numeric($json['id_anamnese_pergunta'])) {
            $sql = "
            UPDATE 
                anamnese_perguntas 
            SET
                label = :label,
                ordem = :ordem
            WHERE 
                id_anamnese_label = :id_anamnese_pergunta
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':label', trim($json['label']));
            $stmt->bindParam(':ordem', trim($json['ordem']));
            $stmt->bindParam(':id_anamnese_pergunta', trim($json['id_anamnese_pergunta']), PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(201);
            $result = array(
                'status' => 'success',
                'result' => 'Atualizados com sucesso!'
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
                'result' => $th->getMessage()
            );
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
