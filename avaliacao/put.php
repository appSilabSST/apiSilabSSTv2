<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_avaliacao']) && is_numeric($json['id_avaliacao']) &&
            isset($json['resultado']) && is_numeric($json['resultado'])
        ) {
            // Prepara os dados com condições na mesma linha
            $avaliacao = (isset($json['avaliacao']) && is_array($json['avaliacao'])) ? json_encode($json['avaliacao']) : null;
            $anamnese = (isset($json['anamnese']) && is_array($json['anamnese'])) ? json_encode($json['anamnese']) : null;
            $anotacao  = (isset($json['anotacao']) && trim($json['anotacao']) !== '') ? trim($json['anotacao']) : null;

            // Prepara o SQL para inserir a avaliação
            $sql = "
              UPDATE 
                avaliacao 
                SET  
                    avaliacao = :avaliacao,
                    resultado = :resultado, 
                    anotacao = :anotacao,
                    anamnese = :anamnese
                WHERE  
                    id_avaliacao = :id_avaliacao  
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':resultado', trim($json['resultado']), PDO::PARAM_INT);
            $stmt->bindParam(':anamnese', $anamnese, $anamnese == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':avaliacao', $avaliacao,  $avaliacao == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':anotacao', $anotacao,  $anotacao == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_avaliacao', trim($json['id_avaliacao']), PDO::PARAM_INT);
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
