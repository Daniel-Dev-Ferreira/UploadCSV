<?php

require_once('App/Model/conexao.php');
require_once('App/Model/eventoCSV.php');

header("Content-Type: text/html; charset=utf-8", true);

$editFormAction = $_SERVER["PHP_SELF"];


if (isset($_SERVER["QUERY_STRING"])) {

    $editFormAction .= "?" . htmlentities($_SERVER["QUERY_STRING"]);
}

$dataAtual = new DateTime('now');

$stringDataAtual =  $dataAtual -> format('Y-m-d');

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <title></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_upload.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Ultra&display=swap" rel="stylesheet">

    <meta charset="UTF-8">

</head>

<style>
    @media print{
        legend, label, #dtInicio, #dtFinal, #Enviar{
            display: none;
        }
    }
</style>


<body>

<div id="title">
    <center>
        <spam>UPLOAD DO ARQUIVO CSV - WEBINAR ZOOM/WEBEX</spam>
    </center>
</div>
<br>

<div id="up">

    <form action="#" method="post" id="form" enctype="multipart/form-data">

        <input class="btn btn-success" type="file" name="file" value="Upload_CSV">

        <br>

        <input style="width: 220px" type="text" id="codEvento" name="codEvento" class="form-control" placeholder="Código Evento" required>

        <input class="btn btn-primary" type="submit" name="button" id="button" value="Enviar Arquivo">

        <br>
        <br>

    </form>

    <br>


    <form id="form2" action="#" method="post">

        <input type="hidden" id="hdAcao" name="hdAcao"	value="Valor Inicial" />

        <select class="form-control" id="tamanho" name="tamanho">
            <option value="">Escolha uma opção</option>
            <option value="InsertNovo">1 - Consulta e Insere novos Participantes</option>
            <option value="Inserir_Inscricao" >2 - Realiza Inscrição dos Participantes Válidos</option>
            <option value="RegistraPontoLote" >3 - Insere Entrada e Saída em lote</option>
            <option value="FrequenciaLote" >4 - Apura Frequência em lote</option>
        </select>

        <input class="btn btn-primary" type="submit" name="button" id="button" value="Buscar">

    </form>

</div>

<br>

<?php

/****************************************************************************************************************

 *      FAZ A LEITURA DO UPLOAD DO ARQUIVO CSV E MONSTRA OS DADOS NA TABELA JA DANDO INSERT NA BASE DE DADOS    *

 * **************************************************************************************************************/

if(isset($_POST["codEvento"])){

    $codEvento = $_POST["codEvento"];
}

if (isset($_FILES["file"])) {
    $arquivo = $_FILES["file"]["tmp_name"];
    $nome = $_FILES["file"]["name"];

    $ext = explode(".", $nome);

    $extensao = end($ext);

    if ($extensao != "csv") {
        echo "Extensão Inválida";
    } else {
        $obj = fopen($arquivo, "r");

        echo("<div class='container'>

            <h2>Dados do Arquivo Importados</h2>
                <table class='table table-striped' style='text-align: center'>
            <tr style='background-color:#337AB7; color:white;'>
                <th  style='text-align: center'>Linha CSV</th>
                <th  style='text-align: center'>Participou</th>
                <th  style='text-align: center'>Nome</th>
                <th  style='text-align: center'>CPF</th>
                <th  style='text-align: center'>E-mail</th>
                <th  style='text-align: center'>Profissão</th>
            </tr>");

        $j = 1;



        // LINHA RESPONSAVEL EM LER O ARQUIVO CSV COM O SEPARADOR SEM UMA VIRGULA
        while (($dados = fgetcsv($obj, 1000, ",")) !== FALSE) {

            echo $column = count($dados);

            if(count($dados) <= 6) {

                $nomeCompleto = $dados[1] . " " . $dados[2];

                // VERIFICA O EMAIL E RETIRA TODOS OS CARACTES QUE NÃO FAZEM PARTE DO CORPO DO EMAIL
                $verficaEmail = filter_var($dados[3], FILTER_SANITIZE_EMAIL);

                // VERIFICA SE EXISTE A COLUNA PROFISSÃO
                if (isset($dados[5])) {
                    $profissaoVerificado = $dados[5];
                } else {
                    $profissaoVerificado = "";
                }

                //RECEBE O CPF QUE VEM DO CSV
                $recebeCPF = $dados[4];

                // CPF VAI RECEBER 11 DIGITOS PREENCHIDOS COM ZERO À ESQUERDA
                $cpf = str_pad($recebeCPF, 11, '0', STR_PAD_LEFT);

                // OBJETO DA CLASSE EVENTOCSV
                $eventoCSV = new eventoCSV($nomeCompleto, $verficaEmail, $cpf, $profissaoVerificado, $stringDataAtual, $codEvento);


                // VAI RECEBER O NOME DO PARTICIPANTE APOS A VALIDAÇÃO DA FUNÇÃO NOME CELULAR
                $nomeVerificado = explode("-", $eventoCSV->nomeAparelhoCelular($eventoCSV->getNome()));

                // VAI RECEBE O CPF APOS TER PASSADO NA FUNÇÃO DE VALIDAÇÃO
                $cpfVerificado = explode('-', $eventoCSV->validaCPF($eventoCSV->getCpf()));

                echo("<tr>
                           <td>$j</td>

                           <td>$dados[0]</td>

                           <td>$nomeCompleto</td>

                           <td>$cpfVerificado[0]</td>

                           <td>$verficaEmail</td>

                           <td>$profissaoVerificado</td>

                      </tr>");

                $j++;

                $csvDao = new CSV_DAO();

                // REALIZANDO TRATRAMENTO SIMPLES PARA INSERÇÃO DOS REGISTROS, ATRAVÉS DO CPF OU DA PARTICIPAÇÃO DO PARTICIPANTE

                if (($dados[0] == "Sim") && ($cpfVerificado[1] == 1) && ($nomeVerificado[1] == 1)) {

                    $eventoCSV->setCpf($cpfVerificado[0]);
                    $csvDao->insertWebinar($eventoCSV);

                } else if (($dados[0] == "Sim") && ($cpfVerificado[1] != 1) && (($nomeVerificado[1] == 1))) {

                    $eventoCSV->setObs('CPF Inválido');
                    $csvDao->insertWebinarInvalido($eventoCSV);

                } else if (($dados[0] == "Sim") && ($cpfVerificado[1] == 1) && ($nomeVerificado[1] != 1)) {

                    $eventoCSV->setObs('Nome Inválido');
                    $csvDao->insertWebinarInvalido($eventoCSV);

                } else if (($dados[0] == "Sim") && ($cpfVerificado[1] != 1) && ($nomeVerificado[1] != 1)) {

                    $eventoCSV->setObs('Nome e Cpf Inválidos');
                    $csvDao->insertWebinarInvalido($eventoCSV);

                } else if ($dados[0] == "Não") {

                    $eventoCSV->setObs('Não Participou');
                    $csvDao->insertWebinarInvalido($eventoCSV);

                }

            }else{

                $csvDao->DeleteWebinar($eventoCSV);
                $csvDao->DeleteWebinarInvalidos($eventoCSV);

                echo "<div class='alert alert-danger' role='alert'>
                       Erro, formato do arquivo CSV inválido, verifique o arquivo na linha $j
                    </div>";

                return false;

            }
        }

        echo "<div class='alert alert-success' role='alert'>
                       Arquivo CSV inserido com sucesso!
                    </div>";
        echo("</table></div> ");

    }

}


/*************************************************************************************************************

 *      SELECIONAR APENAS OS PARTICIPANTES DA TABELA WEBINAR QUE NÃO CONTEM REGISTRO DA TABELA PARTICIPANTES *

 * ***********************************************************************************************************/


if ((isset($_POST["hdAcao"])) && ($_POST["hdAcao"] == "InsertNovo")) {

    $csvDao = new CSV_DAO();
    $i=0;

    ?>

    <div class="container">

        <center><h3>Será inserido apenas os Participantes com dados válidos !</h3></center>

        <form name="form3" id="form3" action="insertParticipante.php" method="post">

            <table border=1 class='table table-striped' style='border:1px solid black'>

                <tr style='background-color:#337AB7; color:white;'>
                    <th></th>
                    <th>Nome Participante</th>
                    <th>CPF Participante</th>
                    <th>E-mail Participante</th>
                </tr>


                <tr>
                    <td class='TDtable1' align='center'><input type='checkbox' class='form-check-input' id='check_tudo' ></td>
                    <th>Selecionar tudo</th>
                    <th></th>
                    <th></th>
                </tr>

                <?php

                foreach ($csvDao->ConsultarParticipantes() as $countParticipante):

                        $i++;

                    echo "
                            <tr>
                                <td class='TDtable1' align='center'><input type='checkbox' name='listaParticipante[]' id='listaParticipante'  value='". $countParticipante['nome'] .",". $countParticipante['cpf'].",". $countParticipante['email']."'>
                                <td>". $countParticipante['nome'] ."</td>
                                <td>". $countParticipante['email'] ."</td>
                                <td>". $countParticipante['cpf'] ."</td>
                            </tr>  ";

                endforeach;

                ?>


                <tr>
                    <th colspan='2' class='TDtable1'style='background-color:#337AB7; color:white; text-align: center' >Total de Registros</th>
                    <th colspan='3' class='TDtable1 bg-success' style="text-align: center" ><?= $i; ?></th>
                </tr>

            </table>

            <br><br>

            <div class='row'>
                <div class='form-group'>
                    <div class='col-md-3'>
                        <input style="margin-left: 20px; " class='btn btn-primary col-md-8' name='insert' type='submit' class='textoNormal' value='Inserir Participantes' />
                        <div id="btn-print"  align="center"><a href="javascript:print();"><img src="../images/impressora.png" width="36px" height="35px" /></a></div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <?php

    /********************************************************************

     *      SELECIONAR TODOS OS PARTICIPANTES DA TABELA WEBINAR         *

     * ******************************************************************/

}

if ((isset($_POST["hdAcao"])) && ($_POST["hdAcao"] == "RegistraPontoLote")) {

    //require_once ("registroPontoEmLote.php");
    header("Location:../pages/registroPontoEmLote.php");


    ?>

<?php }

/*************************************************************************************

 *      SELECIONAR TODOS OS PARTICIPANTES COM DADOS INVÁLIDOS NA TABELA WEBNINAR_2   *

 * ***********************************************************************************/


if ((isset($_POST["hdAcao"])) && ($_POST["hdAcao"] == "FrequenciaLote")) {

    header("Location:../pages/presenca_Manual_Lote.php");
    //require_once ("presenca_Manual_Lote.php");

}



/*************************************************************************************************************************************

 *      SELECIONAR TODOS OS PARTICIPANTES DA TABELA PARTICIPANTES COMPARANDO COM A TABELA WEBINAR PARA INSERIR NA TABELA INSCRIÇÃO   *

 * ***********************************************************************************************************************************/



if ((isset($_POST["hdAcao"])) && ($_POST["hdAcao"] == "Inserir_Inscricao")) { ?>

    <h3>Informe os campos abaixo, para inserir os Participantes na Tabela Inscrição</h3>

    <div class="container">

        <form id="form4" action="insert_Inscricao.php" method="post">

            <div class="container">

                <br><br>

                <fieldset>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3 col-xs-8">
                                <label for="codevento">Código do Evento:</label>
                                <input class="form-control" type="text" id="codEvento" name="codEvento" placeholder="Código do evento" required>
                            </div>

                            <div class="col-md-3 col-xs-8">
                                <label for="codInscricao">Data de Upload CSV:</label>
                                <input class='form-control textoNormal' placeholder='Data de cadastro' name="txtData" id="txtData" type="text" onKeyPress="mascara_data(event, this.value);" maxlength="10" required/>
                            </div>

                            <div class="col-md-2 col-xs-4">
                                <input style="margin-top: 25px; margin-left: 5px" class="form-control btn btn-primary" type="submit" id="buscar" name="buscar" value="Buscar">
                            </div>
                        </div>
                    </div>

                    <br><br>
                    <br><br>

                </fieldset>
        </form>

    </div>

<?php   }

?>

<script src="js/funcoes.js" type="text/javascript"></script>

</body>
</html>



