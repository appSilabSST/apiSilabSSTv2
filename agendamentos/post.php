<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {

        $sql = "
            SELECT 
            IF(((SELECT IFNULL(MAX(nr_agendamento), 0) FROM agendamentos) - ((DATE_FORMAT(CURDATE(), '%y') * 100000))) >= 0,
                (SELECT MAX(nr_agendamento) + 1 FROM agendamentos),
                (DATE_FORMAT(CURDATE(), '%y') * 100000 + 1)
            ) AS next_nr_agendamento
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nr_agendamento = $result['next_nr_agendamento'];

        if (
            isset($json['tipo']) && is_numeric($json['tipo']) && $json['tipo'] == 1 &&
            isset($json['id_tipo_atendimento']) && is_numeric($json['id_tipo_atendimento']) &&
            isset($json['encaixe']) && is_numeric($json['encaixe']) &&
            isset($json['exames']) && count($json['exames']) > 0 &&
            isset($json['riscos']) && count($json['riscos']) > 0
        ) {
            if (isset($json['rl_colaborador_empresa'])) {

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://silabsst.com.br/_backend/colaboradores_empresas/',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($json['rl_colaborador_empresa']),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: $token",
                        "Content-Type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);


                $result = json_decode($response, true);

                if ($result['status'] == "success") {

                    $id_rl_colaborador_empresa = $result['id'];

                    $sql = "
                        INSERT INTO agendamentos 
                            (nr_agendamento, data, horario, id_tipo_atendimento, id_rl_colaborador_empresa, id_profissional, encaixe,observacao) 
                        VALUES
                            (:nr_agendamento,:data, :horario,:id_tipo_atendimento, :id_rl_colaborador_empresa, :id_profissional, :encaixe,:observacao)
                    ";

                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':nr_agendamento', $nr_agendamento, PDO::PARAM_INT);
                    $stmt->bindParam(':data', $json['data']);
                    $stmt->bindParam(':horario', $json['horario']);
                    $stmt->bindParam(':id_tipo_atendimento', trim($json['id_tipo_atendimento']), PDO::PARAM_INT);
                    $stmt->bindParam(':id_rl_colaborador_empresa', $id_rl_colaborador_empresa, PDO::PARAM_INT);
                    $stmt->bindParam(':id_profissional', trim($json['id_profissional']), PDO::PARAM_INT);
                    $stmt->bindParam(':encaixe', trim($json['encaixe']), PDO::PARAM_INT);
                    $stmt->bindParam(':observacao', trim($json['observacao']), trim($json['observacao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                }
            }
        } else if (
            isset($json['tipo']) && is_numeric($json['tipo']) && $json['tipo'] == 2 &&
            isset($json['id_pcmso']) && is_numeric($json['id_pcmso']) &&
            isset($json['id_setor']) && is_numeric($json['id_setor']) &&
            isset($json['id_profissional']) && is_numeric($json['id_profissional']) &&
            isset($json['id_rl_colaborador_empresa']) && is_numeric($json['id_rl_colaborador_empresa']) &&
            isset($json['exames']) && count($json['exames']) > 0 &&
            isset($json['riscos']) && count($json['riscos']) > 0 &&
            isset($json['id_tipo_atendimento'])
        ) {
            $sql = "
                INSERT INTO agendamentos 
                    (nr_agendamento, data, horario,id_pcmso, id_tipo_atendimento, id_rl_colaborador_empresa,id_rl_setor_funcao ,id_setor,id_profissional, encaixe,observacao) 
                VALUES
                    (:nr_agendamento,:data, :horario,:id_pcmso,:id_tipo_atendimento, :id_rl_colaborador_empresa,:id_rl_setor_funcao ,:id_setor,:id_profissional, :encaixe,:observacao)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nr_agendamento', $nr_agendamento, PDO::PARAM_INT);
            $stmt->bindParam(':data', $json['data']);
            $stmt->bindParam(':horario', $json['horario']);
            $stmt->bindParam(':id_pcmso', trim($json['id_pcmso']), trim($json['id_pcmso']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_tipo_atendimento', trim($json['id_tipo_atendimento']), PDO::PARAM_INT);
            $stmt->bindParam(':id_rl_colaborador_empresa', trim($json['id_rl_colaborador_empresa']), PDO::PARAM_INT);
            $stmt->bindParam(':id_rl_setor_funcao', trim($json['id_rl_setor_funcao']), trim($json['id_rl_setor_funcao']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']), PDO::PARAM_INT);
            $stmt->bindParam(':id_setor', trim($json['id_setor']), PDO::PARAM_INT);
            $stmt->bindParam(':encaixe', trim($json['encaixe']), PDO::PARAM_INT);
            $stmt->bindParam(':observacao', trim($json['observacao']), trim($json['observacao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
            exit;
        }

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $id_agendamento = $conn->lastInsertId();

            // Create both cURL resources for exames and riscos
            $ch1 = curl_init();
            $ch2 = curl_init();

            // CHAMA A API PARA CADASTRAR OS EXAMES AO AGENDAMENTO CRIADO
            $postfields = array(
                'id_agendamento' => $id_agendamento,
                'exames' => $json['exames']
            );
            curl_setopt_array($ch1, array(
                CURLOPT_URL => "https://silabsst.com.br/_backend/agendamento_exames/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postfields),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: $token",
                    "Content-Type: application/json"
                ),
            ));

            // CHAMA A API PARA CADASTRAR OS RISCOS AO AGENDAMENTO CRIADO
            $postfields = array(
                'id_agendamento' => $id_agendamento,
                'riscos' => $json['riscos']
            );
            curl_setopt_array($ch2, array(
                CURLOPT_URL => "https://silabsst.com.br/_backend/agendamento_riscos/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postfields),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: $token",
                    "Content-Type: application/json"
                ),
            ));

            // Execute the multi handle for cURL requests
            $mh = curl_multi_init();
            curl_multi_add_handle($mh, $ch1);
            curl_multi_add_handle($mh, $ch2);

            do {
                $status = curl_multi_exec($mh, $active);
                if ($active) {
                    curl_multi_select($mh);
                }
            } while ($active && $status == CURLM_OK);

            // Optionally capture the response
            $response1 = curl_multi_getcontent($ch1);
            $response2 = curl_multi_getcontent($ch2);

            // Close the handles
            curl_multi_remove_handle($mh, $ch1);
            curl_close($ch1);
            curl_multi_remove_handle($mh, $ch2);
            curl_close($ch2);
            curl_multi_close($mh);

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Agendamento atualizado com sucesso!',
                'nr_agendamento' => $nr_agendamento
            );
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Colaborador já existente nesta agenda!',
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
