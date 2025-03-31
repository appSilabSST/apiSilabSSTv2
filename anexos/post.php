<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($_POST['titulo']) &&
            (
                isset($_POST['id_agendamento']) ||
                isset($_POST['id_afastamento']) ||
                isset($_POST['id_rl_agendamento_exame'])
            ) &&
            isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK
        ) {

            //Salva o arquivo no servidor
            $arquivo = move_file($_FILES["arquivo"], $info->nr_doc);

            if ($arquivo) {

                $sql = "INSERT INTO anexos (titulo,arquivo,id_afastamento,id_agendamento,id_rl_agendamento_exame) VALUES (:titulo,:arquivo,:id_afastamento,:id_agendamento,:id_rl_agendamento_exame)";

                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':titulo', trim($_POST['titulo']));
                $stmt->bindParam(':arquivo', trim($arquivo));
                $stmt->bindParam(':id_afastamento', trim($_POST['id_afastamento']), trim($_POST['id_afastamento']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                $stmt->bindParam(':id_agendamento', trim($_POST['id_agendamento']), trim($_POST['id_agendamento']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                $stmt->bindParam(':id_rl_agendamento_exame', trim($_POST['id_rl_agendamento_exame']), trim($_POST['id_rl_agendamento_exame']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);

                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    http_response_code(200);
                    $result = array(
                        'status' => 'success',
                        'result' => 'Sucesso ao adicionar anexo e informações no banco!',
                    );
                } else {
                    http_response_code(500);
                    $result = array(
                        'status' => 'fail',
                        'result' => 'Falha ao inserir os dados do anexo no banco!'
                    );
                }
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao adicionar o Anexo no servidor!'
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

        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
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
