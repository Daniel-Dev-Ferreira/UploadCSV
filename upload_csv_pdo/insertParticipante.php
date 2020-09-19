<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
</html>

<div class="container">

<?php

    require_once ('App/Model/conexao.php');
    require_once ('App/Model/CSV_DAO.php');

    $csvDAO = new CSV_DAO();

    if(!isset($_POST['listaParticipante'] )):
        echo "<script>alert('Erro ao inserir à presença, é preciso selecionar algum <strong>Participante!</strong>')</script>";

    else:

        $csvDAO->InsertParticipante($_POST['listaParticipante']);

    endif;
