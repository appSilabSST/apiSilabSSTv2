<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_agendamento']) && is_numeric($json['id_agendamento']) &&
            isset($json['id_rl_colaborador_empresa']) && is_numeric($json['id_rl_colaborador_empresa']) &&
            isset($json['exames']) && count($json['exames']) > 0 &&
            isset($json['riscos']) && count($json['riscos']) > 0 &&
            isset($json['id_tipo_atendimento'])
        ) {
            $id_agendamento = trim($json['id_agendamento']);

            if (!isset($json['nr_agendamento']) || $json['nr_agendamento'] === null) {
                $sql = "
                    SELECT 
                    IF(((SELECT IFNULL(MAX(nr_agendamento), 0) FROM agendamentos) - ((DATE_FORMAT(CURDATE(), '%y') * 100000))) >= 0,
                        (SELECT MAX(nr_agendamento) + 1 FROM agendamentos),
                        (DATE_FORMAT(CURDATE(), '%y') * 100000 + 1)
                    ) AS next_nr_agendamento
                ";

                $stmt = $conn->prepare($sql); // Use $sql here
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $nr_agendamento = $result['next_nr_agendamento'];
            } else {
                $nr_agendamento = trim($json['nr_agendamento']);
            }

            $sql = "
            UPDATE agendamentos SET
            id_pcmso = :id_pcmso,
            id_tipo_atendimento = :id_tipo_atendimento,
            nr_agendamento = :nr_agendamento,
            id_rl_colaborador_empresa = :id_rl_colaborador_empresa,
            id_empresa_reservado = :id_empresa_reservado,
            id_rl_setor_funcao = :id_rl_setor_funcao,
            funcao = :funcao,
            id_profissional = :id_profissional,
            encaixe = :encaixe,
            disponivel = 0
            WHERE id_agendamento = :id_agendamento
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_pcmso', trim($json['id_pcmso']), trim($json['id_pcmso']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_tipo_atendimento', trim($json['id_tipo_atendimento']), PDO::PARAM_INT);
            $stmt->bindParam(':id_rl_setor_funcao', trim($json['id_rl_setor_funcao']), trim($json['id_rl_setor_funcao']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':funcao', trim($json['funcao']), trim($json['funcao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_empresa_reservado', trim($json['id_empresa_reservado']), trim($json['id_empresa_reservado']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']), PDO::PARAM_INT);
            $stmt->bindParam(':id_rl_colaborador_empresa', trim($json['id_rl_colaborador_empresa']), PDO::PARAM_INT);
            $stmt->bindParam(':encaixe', trim($json['encaixe']), PDO::PARAM_INT);
            $stmt->bindParam(':nr_agendamento', $nr_agendamento, PDO::PARAM_INT);
            $stmt->bindParam(':id_agendamento', $id_agendamento);
            $stmt->execute();

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
                'result' => 'Colaborador já existente nesta agenda!'
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

exit;
