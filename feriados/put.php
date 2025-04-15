<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_feriado']) && is_numeric($json['id_feriado']) &&
            isset($json['periodo']) && is_numeric($json['periodo']) &&
            isset($json['data']) && isset($json['evento'])
        ) {

            $sql = "
            UPDATE feriados SET
            data = :data,
            evento = :evento,
            periodo = :periodo
            WHERE id_feriado = :id_feriado
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data', trim($json['data']));
            $stmt->bindParam(':periodo', trim($json['periodo']), PDO::PARAM_INT);
            $stmt->bindParam(':evento', trim($json['evento']), PDO::PARAM_STR);
            $stmt->bindParam(':id_feriado', trim($json['id_feriado']));
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Cnae atualizado com sucesso!'
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
                'result' => 'Cnae já existente nesta proposta!'
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
