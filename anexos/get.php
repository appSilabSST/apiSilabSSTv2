<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            // Consulta por ID do anexo
            $id_anexo = trim($_GET["id"]);
            $sql = "
                SELECT rl.*
                FROM anexos rl
                WHERE rl.ativo = 1
                AND rl.id_anexo = :id_anexo
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anexo', $id_anexo);
        } elseif (isset($_GET["id_afastamento"]) && is_numeric($_GET["id_afastamento"])) {
            // Consulta por ID do afastamento
            $id_afastamento = trim($_GET["id_afastamento"]);
            $sql = "
                SELECT rl.*
                FROM anexos rl
                WHERE rl.ativo = 1
                AND rl.id_afastamento = :id_afastamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_afastamento', $id_afastamento);
        } elseif (isset($_GET["id_agendamento"]) && is_numeric($_GET["id_agendamento"])) {
            // Consulta por ID do afastamento
            $id_agendamento = trim($_GET["id_agendamento"]);
            $sql = "
                SELECT rl.*
                FROM anexos rl
                WHERE rl.ativo = 1
                AND rl.id_agendamento = :id_agendamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_agendamento', $id_agendamento);
        } elseif (isset($_GET["id_anexo"]) && is_numeric($_GET["id_anexo"])) {
            // Consulta por ID do anexo
            $id_anexo = trim($_GET["id_anexo"]);
            $sql = "
                SELECT rl.*
                FROM anexos rl
                WHERE rl.ativo = 1
                AND rl.id_anexo = :id_anexo
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anexo', $id_anexo);

            // Executa a consulta
            $stmt->execute();
            $anexo = $stmt->fetch(PDO::FETCH_OBJ);

            if ($stmt->rowCount() > 0) {
                // Obtém o diretório raiz do servidor (sem o public_html)
                // Volta um nível a partir do public_html
                // folder oculta de anexos
                $raizServidor = dirname($_SERVER['DOCUMENT_ROOT']) . "/.anexos/";

                // Define o caminho completo para o diretório .anexos
                $caminhoArquivo = $raizServidor . $anexo->arquivo;

                // Verifica se o arquivo existe
                if (file_exists($caminhoArquivo)) {
                    // Define os cabeçalhos para forçar o download
                    header('Content-Description: File Transfer');
                    header('Content-Type: ' . mime_content_type($caminhoArquivo));
                    header('Content-Disposition: attachment; filename="' . basename($caminhoArquivo) . '"');
                    header('Content-Length: ' . filesize($caminhoArquivo));
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Expires: 0');

                    // Envia o arquivo para o cliente
                    readfile($caminhoArquivo);
                    exit; // Encerra o script após enviar o arquivo
                } else {
                    http_response_code(404);
                    $result = json_encode(
                        array(
                            'status' => 'fail',
                            'result' => "Arquivo não encontrado."
                        )
                    );
                    exit;
                }
            } else {
                http_response_code(404);
                $result = json_encode(
                    array(
                        'status' => 'fail',
                        'result' => "Anexo não encontrado."
                    )
                );
                exit;
            }
        } else {
            // Dados incompletos
            http_response_code(400);
            echo json_encode(
                array(
                    'status' => 'fail',
                    'result' => 'Dados incompletos!'
                )
            );
            exit;
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
    } catch (\Throwable $th) {
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
} else {
    // Sem autorização
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
