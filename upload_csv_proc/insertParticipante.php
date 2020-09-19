<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
</html>

    <div class="container">
<?php

require ("conexao.php");

if(!isset($_POST['listaParticipante'] )){
    echo "<script>alert('Erro ao inserir à presença, é preciso selecionar algum <strong>Participante!</strong>')</script>";

} else {

    //PRIMEIRO VAI VERIFICAR SE TEM ALGUM REGISTRO NO REGISTRAPONTO

    mysqli_select_db($conn, "emerjco_evento");

    $k = 0;

    // FAZ UMA VARREDURA
    foreach ($_POST['listaParticipante'] as $lista) {

        $resutLista = explode(",", $lista);

        $passwordDefault = "202cb962ac59075b964b07152d234b70";

        $queryInsertFrequencia = "INSERT INTO participante (nome,cpf, email, senha) VALUES ('".$resutLista[0]."','".$resutLista[1]."','".$resutLista[2]."','".$passwordDefault."')";

        $Result = mysqli_query($conn, $queryInsertFrequencia) or die(mysqli_error($conn));

        $k++;
    }

    $linhaAfetadas = mysqli_affected_rows($conn);


    if ($linhaAfetadas == 0) {
        echo "<script>
              alert('Erro ao Inserir Participante');</script>";
    } else {

        echo "<div class='alert alert-success col-md-6 col-xs-8' style='font-size: 1.2em; margin-top: 50px;'> <strong>" . $k . "</strong> Participantes inseridos na Tabelas Participantes com Sucesso! </div>";

    }
}

?>



<input style="margin-top: 140px; margin-left: -80px; padding: 10px" name="Voltar" type="button" value="VOLTAR" class="btn btn-primary" onClick=top.window.location="index.php"; >


    </div>