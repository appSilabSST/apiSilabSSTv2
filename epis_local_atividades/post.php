<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_grupo_epi']) &&
            isset($json['id_local_atividade'])  &&
            isset($json['epi']) &&
            isset($json['ca'])
        ) {
            $sql = "
            INSERT INTO epis_local_atividades (id_local_atividade, id_grupo_epi,epi, ca) VALUES 
            (:id_local_atividade, :id_grupo_epi, :epi, :ca)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':id_grupo_epi', trim($json['id_grupo_epi']));
            $stmt->bindParam(':epi', trim($json['epi']));
            $stmt->bindParam(':ca', trim($json['ca']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Epi criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o Epis Local de atividade!'
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
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'C.A do epis Local de atividade já existente!'
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
