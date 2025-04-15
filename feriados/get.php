<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_feriado = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM feriados
            WHERE id_feriado = :id_feriado
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_feriado', $id_feriado);
        } else if (isset($_GET["data"])) {
            $data = trim($_GET["data"]);
            $sql = "
            SELECT *
            FROM feriados
            WHERE data = :data
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data', $data);
        } else if (isset($_GET["diaMesAno"])) {
            $daiMesAno = trim($_GET["diaMesAno"]);
            list($ano, $mes, $dia) = explode('/', $daiMesAno);
            $sql = "
            SELECT *
            FROM feriados f
            WHERE YEAR(f.data) = :ano AND MONTH(f.data) = :mes
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':ano', $ano);
            $stmt->bindParam(':mes', $mes);
        } else {
            $sql = "
            SELECT *
            FROM feriados
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
