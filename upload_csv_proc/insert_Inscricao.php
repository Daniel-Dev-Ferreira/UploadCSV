<?php

require_once('conexao.php');

$editFormAction = $_SERVER["PHP_SELF"];

$dataAtual = new DateTime('now');

$dataString = $dataAtual ->format("Y-m-d H:i:s");

if (isset($_POST["codEvento"])) {

    $dataInvertida = $_POST["txtData"];

    $retornaAnoInvertida = substr($dataInvertida, -4);

    $retornaMesInvertida = substr($dataInvertida, 2, -4);

    $retornaDiaInvertida = substr($dataInvertida, 0, 2);

    $retornaDataInvertida = $retornaAnoInvertida . $retornaMesInvertida . $retornaDiaInvertida;

    str_replace("/","",$retornaDataInvertida);

    $query_rsBusca = "SELECT DISTINCT  p.nome, p.cpf, p.email, w.codEvento, p.codigo FROM participante p, webinar w WHERE p.cpf=w.cpf AND w.codEvento = ".$_POST["codEvento"]." AND insert_data = '".$retornaDataInvertida."' order by p.nome ";
    $rsBusca = mysqli_query($conn, $query_rsBusca) or die(mysqli_error($conn));
    $num_row = mysqli_num_rows($rsBusca);


    $rsBusca2 = mysqli_query($conn, $query_rsBusca) or die(mysqli_error($conn));
    $total_row = mysqli_num_rows($rsBusca2);
    $result_assoc = mysqli_fetch_assoc($rsBusca2);

}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">

</head>
<body>

<div class="container">

    <br><br>

<?php try{

    if (isset($_POST["codEvento"])) {

    if($num_row == 0 ){
        echo"<script>alert('Evento não encontrado no Webinar!');</script>";

        echo" <h3 style='text-align: center'>Erro ao consultar o código Evento ! </h3>";

        echo"<br>";

        echo "<center><input name='Voltar' type='button' value='Voltar Página Anterior' class='btn btn-primary' onClick=top.window.location='index.php'; ></center>";
    } else{


    ?>

    <form name="form" id="form" action="<?php echo $editFormAction; ?>" method="post">

        <h3 style="text-align: center">Lista de Participantes do Evento <strong><?php echo $result_assoc["codEvento"]; ?></strong> para inserir na Tabela Inscrição</h3>

    <table class="TDtable1 table table-striped" align="center" border=1 cellpadding="1" cellspacing="1" style="width:'910px';">

        <tr style='background-color:#337AB7; color:white;' class="TDdata">
            <th></th>
            <th style = "width:3%;  text-align: center;">Num.</th>
            <th style = "width:30%; text-align: center;">Nome Participante</th>
            <th style = "width:30%; text-align: center;">E-mail</th>
            <th style = "width:20%; text-align: center;">CPF</th>
            <th style = "width:30%; text-align: center;">Código Evento</th>
            <th style = "width:30%; text-align: center;">Código Participante</th>
        </tr>

        <tr>
            <th style="text-align: center"><input type='checkbox' class='form-check-input' id='check_tudo' style="margin-left: 5px;"></th>
            <th style="text-align: center">0</th>
            <th style="text-align: center">SELECIONAR TODOS</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>

        <?php  $i = 1;

        while ($vet=mysqli_fetch_row($rsBusca)) {

            echo "<tr class='TDtable1'>
                    <td class='TDtable1' align='center'><input type='checkbox' name='listaParticipante[]' id='listaParticipante'  value='". $vet[0] .",". $vet[1].",". $vet[2].",". $vet[3].",". $vet[4]."'>
                    <td class='TDtable1' align='center'>" . $i . "</td>
                    <td class='TDtable1' align='center'>$vet[0]</td>
                    <td class='TDtable1' align='center'>$vet[2]</td>
                    <td class='TDtable1' align='center'>$vet[1]&nbsp;</td>
                    <td class='TDtable1' align='center'>$vet[3]&nbsp;</td>
                    <td class='TDtable1' align='center'>$vet[4]&nbsp;</td>
                  </tr>";

             $i++;
        }

        ?>

        <tr>
            <th colspan='4' class='TDtable1'style='background-color:#337AB7; color:white; text-align: center' >Total de Registros</th>
            <th colspan='3' class='TDtable1 bg-success' style="text-align: center" ><?php echo $total_row; ?></th>
        </tr>

    </table>

        <br><br>

        <div class='row'>
            <div class='form-group'>
                <div class='col-md-3'>
                    <input style="margin-left: 20px; " class='btn btn-primary col-md-8' name='insert' type='submit' class='textoNormal' value='Inserir Inscrições' />
                </div><div class='col-md-3'>
                    <input name="Voltar" type="button" value="Voltar Página Anterior" class="btn btn-primary" onClick=top.window.location="index.php"; >
                </div>
            </div>
        </div>
    </form>

    <br/><br/>

<?php   } }  }

    catch (mysqli_sql_exception $exception){

        echo "Erro " . $exception;

    }

    if(isset($_POST['listaParticipante'] )) {

        $k = 0;
        $l = 0;

        foreach ($_POST['listaParticipante'] as $lista) {

            $resutLista = explode(",", $lista);

            $codEventoVerificado = mysqli_real_escape_string($conn, $resutLista[3]);
            $codParticipanteVerificado = mysqli_real_escape_string($conn, $resutLista[4]);

             $query_rsBuscaInscricao = 'SELECT i.codEvento, i.codParticipante 

             FROM inscricoes i WHERE ((i.codEvento = "' . $codEventoVerificado .'") AND (i.codParticipante = "' . $codParticipanteVerificado . '"))';
             $consultaResult = mysqli_query($conn, $query_rsBuscaInscricao);
             $result_row = mysqli_num_rows($consultaResult);

             if($result_row == 1){
                 $l++;

             } else {

                 $sqlInsert = "INSERT INTO inscricoes (codEvento, codParticipante, impedido, observacao, dataHoraInscricao) VALUES ('$codEventoVerificado','$codParticipanteVerificado','0',' ','$dataString')";
                 $Result = mysqli_query($conn, $sqlInsert) or die(mysqli_error($conn));

                 $k++;

             }
        }

        $linhaAfetadas = mysqli_affected_rows($conn);

        if ($linhaAfetadas == 0) {
            echo "<script>
                  alert('Erro no insert');</script>";

        } else {
            echo "<div class='container'><div class='alert alert-success col-md-6 col-xs-8' style='font-size: 1.2em;'> <strong>" . $k . "</strong> Presenças realizadas com Sucesso! </div></div>";
            echo "<br>";
            echo "<div class='container'><div class='alert alert-danger col-md-6 col-xs-8' style='font-size: 1.2em;'> <strong>" . $l . "</strong> Presenças já constava na Tabela de Inscrição! </div></div>";
            echo"<input style='margin-left: 15px;' name='Voltar' type='button' value='Voltar Página Anterior' class='btn btn-primary' onClick=top.window.location='index.php'; >";

        }
    }

    ?>



</div>

<script type="text/javascript">

    // MARCAR TODOS OS CHECKBOX

    $('#check_tudo').click(function () {
        // marca ou desmarca os outros checkbox

        $('input:checkbox').not(this).prop('checked', this.checked);

    });

</script>

</body>

</html>