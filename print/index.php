<?php

if(!empty($_GET["tipo_documento"]) && !empty($_GET["id"])) {

    include_once('../conexao.php');

    $id_documento = trim($_GET["id"]);
    $tipo_documento = trim($_GET["tipo_documento"]);

    $sql = "
    SELECT corpo_documento
    FROM $tipo_documento
    WHERE id_" . $tipo_documento . " = $id_documento
    ";

    // echo $sql;exit;

    $query = mysqli_query($conecta, $sql);

    if(mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_object($query);

        echo '
        <!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Impressão de documento</title>
        </head>
        <body>
            '.$row->corpo_documento.'
            
            <script>
                window.print();
                
                window.addEventListener("cancel", function(event) { window.close(); });
                
                window.oncancel();
            </script>

        </body>
        </html>
        ';

    }

}

?>