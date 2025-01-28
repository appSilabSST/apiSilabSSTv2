<?php
// Check if access is authorized
if ($authorization) {
    try {
        // Prepare SQL based on the presence of an ID
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_formulario = trim($_GET["id"]);
            $formSql = "
            SELECT id_formulario, formulario, status, linhas
            FROM formularios
            WHERE id_formulario = :id_formulario
            ";
            $stmt = $conn->prepare($formSql);
            $stmt->bindParam(':id_formulario', $id_formulario);
        } else {
            $formSql = "SELECT * FROM formularios";
            $stmt = $conn->prepare($formSql);
        }

        // Execute SQL
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $formData = $stmt->fetchAll(PDO::FETCH_OBJ);

            if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
                $componentSql = "
                SELECT c.id_componente, c.componente, c.tipo,
                rl_fc.id_rl_formulario_componente, rl_fc.label, rl_fc.obrigatorio, rl_fc.tamanho, rl_fc.linha
                FROM componentes c
                JOIN rl_formularios_componentes rl_fc ON c.id_componente = rl_fc.id_componente
                WHERE rl_fc.id_formulario = :id_formulario
                ORDER BY rl_fc.linha,rl_fc.ordem
                ";

                $stmt = $conn->prepare($componentSql);
                $stmt->bindParam(':id_formulario', $id_formulario);
                $stmt->execute();

                $formData[0]->componentes = $stmt->fetchAll(PDO::FETCH_OBJ);

                // Fetch options for each component
                foreach ($formData[0]->componentes as $key => $component) {
                    $optionSql = "
                    SELECT rl_co.id_rl_componente_opcao, opcao
                    FROM rl_componentes_opcoes rl_co
                    WHERE rl_co.id_rl_formulario_componente = :id_rl_formulario_componente
                    ";

                    $stmt = $conn->prepare($optionSql);
                    $stmt->bindParam(':id_rl_formulario_componente', $component->id_rl_formulario_componente);
                    $stmt->execute();

                    $formData[0]->componentes[$key]->opcoes = $stmt->fetchAll(PDO::FETCH_OBJ);
                }
            }

            $result = [
                'status' => 'success',
                'result' => $formData
            ];
        } else {
            $result = [
                'status' => 'fail',
                'result' => 'Nenhum formulário foi encontrado'
            ];
        }
    } catch (\Throwable $th) {
        $result = [
            'status' => 'fail',
            'result' => $th->getMessage()
        ];
    } finally {
        $conn = null;
        echo json_encode($result);
    }
} else {
    http_response_code(403);
    echo json_encode([
        'status' => 'fail',
        'result' => 'Sem autorização para acessar este conteúdo!'
    ]);
}
exit;
