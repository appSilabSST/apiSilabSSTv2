<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // Verificação mais robusta para campos obrigatórios
        if (
            isset($json['id_tipo_orgao']) && isset($json['nr_doc']) && isset($json['razao_social']) &&
            isset($json['cep']) && isset($json['logradouro']) && isset($json['numero']) &&
            isset($json['bairro']) && isset($json['cidade']) && isset($json['uf'])
        ) {

            $sql = "
                INSERT INTO empresas 
                    (razao_social, nome_fantasia, id_tipo_orgao, nr_doc, nr_doc_matriz, inscricao_estadual, cep, logradouro, numero, complemento, bairro, cidade, uf) 
                VALUES
                    (:razao_social, :nome_fantasia, :id_tipo_orgao, :nr_doc, :nr_doc_matriz, :inscricao_estadual, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf)
            ";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':razao_social', trim($json['razao_social']));
            $stmt->bindParam(':nome_fantasia', trim($json['nome_fantasia']));
            $stmt->bindParam(':id_tipo_orgao', trim($json['id_tipo_orgao']));
            $stmt->bindParam(':nr_doc', trim($json['nr_doc']));
            $stmt->bindParam(':nr_doc_matriz', trim($json['nr_doc_matriz']));
            $stmt->bindParam(':inscricao_estadual', trim($json['inscricao_estadual']), trim($json['inscricao_estadual']) === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cep', trim($json['cep']), trim($json['cep']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':logradouro', trim($json['logradouro']), trim($json['logradouro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':numero', trim($json['numero']), trim($json['numero']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':complemento', trim($json['complemento']), trim($json['complemento']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':bairro', trim($json['bairro']), trim($json['bairro']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cidade', trim($json['cidade']), trim($json['cidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':uf', trim($json['uf']), trim($json['uf']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            $stmt->execute();


            if ($stmt->rowCount() > 0) {

                $id_empresa = $conn->lastInsertId();

                // $cnae = null;

                // if (is_array($json['cnaes'])) {
                //     // pega o cnae primario da empresa
                //     $cnae = array_filter($json['cnaes'], function ($element) {
                //         return $element['classe'] == 1;
                //     });

                //     if (isset($cnae)) {
                //         // Pega o primeiro CNAE filtrado
                //         $cnae = array_values($cnae)[0];
                //     }
                // }

                // Cria o manipulador multi-cURL
                $mh = curl_multi_init();

                // Para armazenar os handles individuais de cURL
                $handles = [];

                if ($json['id_tipo_orgao'] == 2) {
                    $nr_inscricao = $json['nr_doc'];
                    $id_tipo_orgao = $json['id_tipo_orgao'];
                } else {
                    $nr_inscricao = null;
                    $id_tipo_orgao = 3;
                }

                // Chama a API para cadastrar o local de atividade
                $postfields = array(
                    'id_empresa' => $id_empresa,
                    'razao_social' => $json['razao_social'],
                    'id_tipo_ambiente' => 1,
                    'nr_inscricao' => $nr_inscricao,
                    'id_tipo_orgao' => $id_tipo_orgao,
                    'codigo' => $json['codigo'],
                    'atividade' => $json['atividade'],
                    'grau_risco' => $json['grau_risco'],
                    'atividade_principal' => $json['atividade_principal'],
                    'logradouro' =>  $json['logradouro'],
                    'numero' => $json['numero'],
                    'complemento' =>  $json['complemento'],
                    'bairro' =>  $json['bairro'],
                    'cidade' => $json['cidade'],
                    'cep' => $json['cep'],
                    'uf' =>  $json['uf'],
                );

                // Inicializa o handle para a primeira API (local de atividade)
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => "https://silabsst.com.br/_backend/locais_atividade/",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,  // Timeout de 30 segundos
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($postfields),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: $token",
                        "Content-Type: application/json"
                    ),
                ));

                curl_multi_add_handle($mh, $ch);
                // Armazena o handle
                $handles[] = $ch;
                // if (is_array($json['cnaes'])) {
                //     // Loop através dos CNAEs para adicionar os vínculos
                //     foreach ($json['cnaes'] as $cnaeItem) {
                //         if (isset($cnaeItem['id_cnae'])) {
                //             $postfields = array(
                //                 'id_cnae' => $cnaeItem['id_cnae'],
                //                 'classe' => $cnaeItem['classe'],
                //                 'id_empresa' => $id_empresa
                //             );

                //             // Inicializa um novo handle para cada CNAE
                //             $ch = curl_init();
                //             curl_setopt_array($ch, array(
                //                 CURLOPT_URL => "https://silabsst.com.br/_backend/cnae_empresa/",
                //                 CURLOPT_RETURNTRANSFER => true,
                //                 CURLOPT_ENCODING => '',
                //                 CURLOPT_MAXREDIRS => 10,
                //                 CURLOPT_TIMEOUT => 30,  // Timeout de 30 segundos
                //                 CURLOPT_FOLLOWLOCATION => true,
                //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //                 CURLOPT_CUSTOMREQUEST => 'POST',
                //                 CURLOPT_POSTFIELDS => json_encode($postfields),
                //                 CURLOPT_HTTPHEADER => array(
                //                     "Authorization: $token",
                //                     "Content-Type: application/json"
                //                 ),
                //             ));

                //             // Adiciona este handle ao multi-cURL
                //             curl_multi_add_handle($mh, $ch);
                //             $handles[] = $ch;  // Armazena o handle para posterior processamento
                //         }
                //     }
                // }

                // Executa todas as requisições em paralelo
                $running = null;
                do {
                    curl_multi_exec($mh, $running);
                    usleep(100);  // Pequeno delay para reduzir carga no servidor
                } while ($running);

                // Processa as respostas
                $resultVinculoCnae = [];
                foreach ($handles as $ch) {
                    $response = curl_multi_getcontent($ch);

                    if ($response === false) {
                        $resultVinculoCnae[] = "Error calling API.\n";
                    } else {
                        $resultVinculoCnae[] =  "API Response: $response\n";
                    }

                    // Remove o handle do multi-cURL e fecha-o
                    curl_multi_remove_handle($mh, $ch);
                    curl_close($ch);
                }

                // Fecha o manipulador multi-cURL
                curl_multi_close($mh);

                // Loga o resultado para depuração
                file_put_contents("logs.txt", "Empresa cadastrada: " . json_encode($json) . "\n", FILE_APPEND);

                // Retorna a resposta de sucesso
                $result = array(
                    'status' => 'success',
                    'result' => 'Empresa cadastrada, local de atividade e vínculos com CNAE com sucesso!',
                    'result2' => $resultVinculoCnae,
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar empresa!'
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
                'result' => 'Empresa já existente!'
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
