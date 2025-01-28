<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if ((isset($json["id_anamnese"]) && is_numeric($json["id_anamnese"])) &&  (isset($json["ordem"]) && is_numeric($json["ordem"]))  &&  isset($json["pergunta"])) {

            $sql = "INSERT INTO anamnese_perguntas (pergunta,ordem,id_anamnese) VALUES (:pergunta,:ordem,:id_anamnese)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese', trim($json['id_anamnese']), PDO::PARAM_INT);
            $stmt->bindParam(':ordem', trim($json['ordem']));
            $stmt->bindParam(':pergunta', trim($json['pergunta']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar a pergunta!'
                );
            }
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
