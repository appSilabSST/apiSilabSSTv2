<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AGENDAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_formulario_componente = trim($_GET["id"]);
            $sql = "
            SELECT *, rl_formularios_componentes.ordem as rl_f_c_ordem,GROUP_CONCAT(rl_componentes_opcoes.opcao) as opcao
            FROM rl_formularios_componentes 
            LEFT JOIN rl_componentes_opcoes ON rl_componentes_opcoes.id_rl_formulario_componente = rl_formularios_componentes.id_rl_formulario_componente 
            WHERE rl_formularios_componentes.id_rl_formulario_componente =:id_rl_formulario_componente
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_formulario_componente', $id_rl_formulario_componente);
        } else if (isset($_GET["id_formulario"]) && is_numeric($_GET["id_formulario"])) {
            $id_formulario = trim($_GET["id_formulario"]);
            $sql = "SELECT 
                        *,
                        (
                            SELECT MAX(ordem) 
                            FROM rl_formularios_componentes
                        ) AS max_ordem 
                    FROM 
                        rl_formularios_componentes 
                        JOIN componentes ON componentes.id_componente =  rl_formularios_componentes.id_componente 
                    WHERE 
                        rl_formularios_componentes.id_formulario = :id_formulario
                    ORDER BY
                        rl_formularios_componentes.linha   ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_formulario', $id_formulario);
        }
        // RETORNA MENSAGEM INFORMAÇÃO A OBRIGATORIEDADE EM ENVIAR UMA DATA
        else {
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
            echo json_encode($result);
            exit;
        }
        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum agendamento foi encontrado'
            );
        } elseif ($stmt->rowCount() == 1 && isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $dados = $stmt->fetch(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        } else {
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        }
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
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
