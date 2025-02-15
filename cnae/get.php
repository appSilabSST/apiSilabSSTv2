<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_cnae = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM cnae
            WHERE ativo = '1'
            AND id_cnae = :id_cnae
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_cnae', $id_cnae);
        } else if (isset($_GET["codigo"]) && is_numeric($_GET["codigo"])) {
            $codigo = trim($_GET["codigo"]);
            $sql = "
            SELECT *
            FROM cnae
            WHERE ativo = '1'
            AND codigo = :codigo
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':codigo', $codigo);
        } else if (isset($_GET["cnaes"]) && is_numeric($_GET["cnaes"])) {

            $postfields = [];

            // Abre o arquivo txt
            $file = fopen('../cnae.txt', 'r');
            // Lê o arquivo linha por linha
            while (($line = fgets($file)) !== false) {
                // Remove o caractere de quebra de linha no final da linha
                $line = trim($line);

                // Separa o código e a descrição
                list($codigo, $atividade, $dataInicio, $dataFim, $aliquita) = explode('|', $line);

                // Preenche o campo $codigo com zeros à esquerda até que ele tenha 6 caracteres
                $codigo = str_pad($codigo, 6, '0', STR_PAD_LEFT);

                //no arquivo txt ele tráz os cnae finalizado e atual, para não ter duplicidade if ignorar os cnae com data fim
                if (!empty($dataFim)) {
                    // Monta os dados a serem enviados via POST
                    $postfields[] = array(
                        'codigo' => $codigo,
                        'atividade' => $atividade
                    );
                }
            }
            // Fecha o arquivo
            fclose($file);
            $conn = null;
            echo json_encode($postfields);
            exit;
        } else {
            $sql = "
            SELECT *
            FROM cnae
            WHERE ativo = '1'
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
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
}
