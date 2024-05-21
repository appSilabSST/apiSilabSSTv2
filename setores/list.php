<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];
    $id_local_atividade = $postjson['id_local_atividade'];
    $id_pgr = $postjson['id_pgr'];
    $id_ltcat = $postjson['id_ltcat'];
    $id_pcmso = $postjson['id_pcmso'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND s.id_setor = $id
        ";
    }

    // SE TIVER ID_LOCAL_ATIVIDADE PARA BUSCA
    if ($id_local_atividade > 0) {
        $where .= "
        AND s.id_local_atividade = $id_local_atividade
        ";
    }

    // SE TIVER ID_PGR PARA ENVIAR NO MODAL
    if ($id_pgr > 0) {
        $where_pgr = "
        , $id_pgr id_pgr
        ";
    }

    // SE TIVER ID_PGR PARA ENVIAR NO MODAL
    if ($id_ltcat > 0) {
        $where_ltcat = "
        , $id_ltcat id_ltcat
        ";
    }

    // SE TIVER ID_PCMSO PARA ENVIAR NO MODAL
    if ($id_pcmso > 0) {
        $where_pcmso = "
        , $id_pcmso id_pcmso
        ";
    }

    $sql = "
    SELECT s.id_setor,s.setor,s.descricao,s.conclusao,s.status,
    lt.id_local_atividade,lt.razao_social,
    (
        SELECT GROUP_CONCAT(IF(LENGTH(r.cod) > 0, CONCAT_WS(' | eSocial: ' , r.procedimento , r.cod), r.procedimento) ORDER BY r.procedimento SEPARATOR '<br>')
        FROM rl_setores_exames AS b
        JOIN exames r ON b.id_exame = r.id_exame
        WHERE b.id_setor = s.id_setor
    ) lista_exames,
    (
        SELECT GROUP_CONCAT(a.funcao ORDER BY a.funcao)
        FROM rl_setores_funcoes AS a
        WHERE a.id_setor = s.id_setor
    ) lista_funcoes,
    (
        SELECT GROUP_CONCAT(IF(LENGTH(r.cod) > 0, CONCAT_WS(' | eSocial: ' , r.descricao , r.cod), r.descricao) ORDER BY r.descricao SEPARATOR '<br>')
        FROM rl_setores_riscos AS b
        JOIN riscos r ON b.id_risco = r.id_risco
        WHERE b.id_setor = s.id_setor
    ) lista_riscos
    $where_pgr
    $where_pcmso
    $where_ltcat
    FROM setores AS s
    LEFT JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
    WHERE s.ativo = 1
    $where
    ORDER BY s.setor
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            // FORMATAR STATUS
            if ($row->status == 0) {
                $row->status_format = '
                    <div class="alert mb-0 alert-danger text-center" role="alert">
                    Inativo
                    </div>';
            } elseif ($row->status == 1) {
                $row->status_format = '
                    <div class="alert mb-0 alert-success text-center" role="alert">
                    Ativo
                    </div>
                    ';
            }


            // VERIFICA SE HÁ FUNÇÕES NO SETOR
            if (empty($row->lista_funcoes)) {
                $row->lista_funcoes = "Nenhuma Função foi encontrada";
            }

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum Setor foi encontrado.'
        ));
        echo $result;
        exit;
    }

    if ($query) {
        $result = json_encode(array(
            'success' => true,
            'result' => $dados
        ));
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Falha ao carregar registros.'
        ));
    }

    echo $result;
    exit;
}
