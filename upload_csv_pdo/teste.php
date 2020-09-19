<?php
    require_once ('App/Model/CSV_DAO.php');
?>

<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
</html>

<body>



<?php

    $csvDaoInsert   = new CSV_DAO();
    $csvDaoInsert->insertInscricao($_POST['listaParticipante']);

?>

</body>