<?php

require ("conexao.php");

$editFormAction = $_SERVER["PHP_SELF"];
if (isset($_SERVER["QUERY_STRING"])) {
    $editFormAction .= "?" . htmlentities($_SERVER["QUERY_STRING"]);

}

header("Content-Type: text/html; charset=utf8", true);

$dataAtual = new DateTime('now');

$stringDataAtual =  $dataAtual -> format('Y-m-d');

//FUNÇÃO PARA INVALIDAR NOMES DE APARELHOS CELULARES
function nomeAparelhoCelular($nomeCompletoVerficado)
{
    $verificarNomeInvalido = strtolower($nomeCompletoVerficado);
    $nomeCelular = '';
    $nomeCelulares = array('asus', 'galaxy', 'samsung', 'motorola','moto');

    foreach ($nomeCelulares as $value) {
        $pos = strpos($verificarNomeInvalido, $value);

        if (!($pos === false))
            $nomeCelular = $nomeCelular . $value . '|';
    }

    if (strlen($nomeCelular) > 1) {
        return $nomeCompletoVerficado . "-" . false;
    } else {
        return $nomeCompletoVerficado . "-" . true;
    }

}

// FUNÇÃO PARA VALIDAR CPF
function validaCPF($cpf) {

    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return $cpf . "-" . false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11 ou 999.999.999-99
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return $cpf . "-" . false;
    }

    // Faz o calculo para validar o CPF de acordo com a Receita Federal
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf{$c} * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf{$c} != $d) {
            return $cpf . "-" . false;
        }
    }

    return $cpf . "-" . true;

}

?>

<html>

    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style_upload.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Ultra&display=swap" rel="stylesheet">
        <meta charset="UTF-8">
    </head>

    <body>
    <br>
        <div id="up">
            <form action="#" method="post" id="form" enctype="multipart/form-data">

                <h2 id="up_csv">Upload de Arquivo CSV</h2>
                <input class="btn btn-success" type="file" name="file" value="Upload_CSV">

                <br>

                <input style="width: 277px" type="text" id="codEvento" name="codEvento" class="form-control" placeholder="Código Evento" required>

                <input class="btn btn-primary" type="submit" name="button" id="button" value="Enviar Arquivo">
            </form>

            <form id="form2" action="#" method="post">

                <input type="hidden" id="hdAcao" name="hdAcao"	value="Valor Inicial" />
                <select class="form-control" id="tamanho" name="tamanho">
                    <option value="">Escolha uma opção</option>
                    <option value="InsertNovo">Consulta e Inserção de novos Participantes</option>
                    <option value="InsertTodos" >Consulta todos os Participantes Webinar</option>
                    <option value="Problemas" >Consultar os Participantes com Dados incorretos</option>
                    <option value="Inserir_Inscricao" >Inserção na Tabale Inscrição dos Participantes Válidos</option>
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

                    $nomeCompleto = $dados[1] . " " . $dados[2];

                    // VERIFICA O EMAIL E RETIRA TODOS OS CARACTES QUE NÃO FAZEM PARTE DO CORPO DO EMAIL
                    $verficaEmail = filter_var($dados[3], FILTER_SANITIZE_EMAIL);

                    $nomeCompletoVerficado = mysqli_real_escape_string($conn, $nomeCompleto);
                    $emailVerificado       = mysqli_real_escape_string($conn, $verficaEmail);

                    if(isset($dados[5])){
                        $profissaoVerificado   = mysqli_real_escape_string($conn, $dados[5]);
                    }else {
                        $profissaoVerificado = "";
                    }

                    //RECEBE O CPF QUE VEM DO CSV
                    $recebeCPF = $dados[4];

                    // CPF VAI RECEBER 11 DIGITOS PREENCHIDOS COM ZERO À ESQUERDA
                    $cpf = str_pad($recebeCPF,11,'0', STR_PAD_LEFT);

                    // VAI RECEBE O CPF APOS TER PASSADO NA FUNÇÃO DE VALIDAÇÃO
                    $cpfVerificado = "";
                    $cpfVerificado = explode("-",validaCPF($cpf));

                    // VAI RECEBER O NOME DO PARTICIPANTE APOS A VALIDAÇÃO DA FUNÇÃO NOME CELULAR
                    $resultTodosNomes = explode("-",nomeAparelhoCelular($nomeCompletoVerficado));

                    echo("<tr>
                           <td>$j</td>
                           <td>$dados[0]</td>
                           <td>$nomeCompletoVerficado</td>
                           <td>$cpfVerificado[0]</td>
                           <td>$emailVerificado</td>
                           <td>$profissaoVerificado</td>
                      </tr>");


                    $j++;

                    // REALIZANDO TRATRAMENTO SIMPLES PARA INSERÇÃO DOS REGISTROS, ATRAVÉS DO CPF OU DA PARTICIPAÇÃO DO PARTICIPANTE

                    if(($dados[0] == "Sim") && ($cpfVerificado[1] == 1) && ($resultTodosNomes[1] == 1) ){

                        $insert = "INSERT INTO webinar (nome,email,cpf,profissao,insert_data, codEvento) VALUES ('" . $resultTodosNomes[0] . "','" . $emailVerificado . "','" . $cpfVerificado[0] . "','" . $profissaoVerificado . "','" . $stringDataAtual . "','" . $codEvento . "')";
                        $Result = mysqli_query($conn, $insert) or die(mysqli_error($conn));


                    } else if (($dados[0] == "Sim") && ($cpfVerificado[1] != 1) && (($resultTodosNomes[1] == 1))) {
                        $insert = "INSERT INTO webinar_invalidos (nome,email,cpf,profissao,obs,insert_data, codEvento) VALUES ('" . $resultTodosNomes[0] . "','" . $emailVerificado . "','" . $cpfVerificado[0] . "','" . $profissaoVerificado . "', 'CPF Inválido !','".$stringDataAtual."','" . $codEvento . "')";
                        $Result = mysqli_query($conn, $insert) or die(mysqli_error($conn));
                    }

                     else if (($dados[0] == "Sim") && ($cpfVerificado[1] == 1) && ($resultTodosNomes[1] != 1)) {
                        $insert = "INSERT INTO webinar_invalidos (nome,email,cpf,profissao,obs,insert_data, codEvento) VALUES ('" . $resultTodosNomes[0] . "','" . $emailVerificado . "','" . $cpfVerificado[0] . "','" . $profissaoVerificado . "', 'Nome Inválido !','".$stringDataAtual."','" . $codEvento . "')";
                        $Result = mysqli_query($conn, $insert) or die(mysqli_error($conn));
                    }

                    else if (($dados[0] == "Sim") && ($cpfVerificado[1] != 1) && ($resultTodosNomes[1] != 1)) {
                        $insert = "INSERT INTO webinar_invalidos (nome,email,cpf,profissao,obs,insert_data, codEvento) VALUES ('" . $resultTodosNomes[0] . "','" . $emailVerificado . "','" . $cpfVerificado[0] . "','" . $profissaoVerificado . "', 'Mais de um dado Inválido !','".$stringDataAtual."','" . $codEvento . "')";
                        $Result = mysqli_query($conn, $insert) or die(mysqli_error($conn));
                    }

                    else if ($dados[0] == "Não"){

                        $insert = "INSERT INTO webinar_invalidos (nome,email,cpf,profissao,obs,insert_data, codEvento) VALUES ('" . $resultTodosNomes[0] . "','" . $emailVerificado . "','" . $cpfVerificado[0] . "','" . $profissaoVerificado . "', 'Não Participou !','".$stringDataAtual."','" . $codEvento . "')";
                        $Result = mysqli_query($conn, $insert) or die(mysqli_error($conn));
                    }
                }

                echo("</table></div> ");
            }
        }


        /*************************************************************************************************************
         *      SELECIONAR APENAS OS PARTICIPANTES DA TABELA WEBINAR QUE NÃO CONTEM REGISTRO DA TABELA PARTICIPANTES *
         * ***********************************************************************************************************/

        if ((isset($_POST["hdAcao"])) && ($_POST["hdAcao"] == "InsertNovo")) {

            $consultarParticipante = "SELECT distinct p.nome, p.cpf, p.email, p.codEvento from webinar p where cpf not in (select cpf from participante) ORDER BY `nome` ASC ";
            $retorno_Query = mysqli_query($conn, $consultarParticipante) or die(mysqli_error($conn));
            $retorno_Query2 = mysqli_query($conn, $consultarParticipante) or die(mysqli_error($conn));

            $rowResult2 = mysqli_fetch_row($retorno_Query2);
            $totalrow = mysqli_num_rows($retorno_Query);



            ?>


     <div class="container">

         <center><h3>Será inserido apenas os Participantes com dados válidos !</h3></center>

    <form name="form3" id="form3" action="insertParticipante.php" method="post">

    <table border=1 class='table table-striped' style='border:1px solid black'>

        <tr style='background-color:#337AB7; color:white;'>
            <th colspan="2">Nome Participante</th>
            <th>CPF Participante</th>
            <th>E-mail Participante</th>
            <th>Código Evento</th>
        </tr>

        <tr>
            <th class='TDtable1' align='center'><input type='checkbox' class='form-check-input' id='check_tudo' style="margin-left: 5px;"></th>
            <th>Selecionar tudo</th>
            <th></th>
            <th></th>
        </tr>

    <?php

    $array = array();
    $nof = mysqli_num_fields($retorno_Query);
    while ($rowResult = mysqli_fetch_row($retorno_Query)){

        $verificarNome = str_replace("'","", $rowResult[0] );


       echo"
                <tr>
                    <td class='TDtable1' align='center'><input type='checkbox' name='listaParticipante[]' id='listaParticipante'  value='". $verificarNome .",". $rowResult[1].",". $rowResult[2]."'>
                    <td> $rowResult[0]</td>
                    <td> $rowResult[1]</td>
                    <td> $rowResult[2]</td>
                    <td> $rowResult[3]</td>
               </tr>
               
               
           ";
    }

    ?>


        <tr>
            <th colspan='2' class='TDtable1'style='background-color:#337AB7; color:white; text-align: center' >Total de Registros</th>
            <th colspan='3' class='TDtable1 bg-success' style="text-align: center" ><?php echo $totalrow; ?></th>
        </tr>

        </table>

        <br><br>


        <div class='row'>
            <div class='form-group'>
                <div class='col-md-3'>
                    <input style="margin-left: 20px; " class='btn btn-primary col-md-8' name='insert' type='submit' class='textoNormal' value='Inserir Participantes' />


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

        if ((isset($_POST["hdAcao"])) && ($_POST["hdAcao"] == "InsertTodos")) {


        echo" <div class='container'>
    
        <div id='buscar'>

            <p id='pesquisar_codEvento'>Pesquisar Código do Evento</p>
        
            <form method='POST' id='form-pesquisa' action=''>
                <div class='row'>
                    <div class='col-md-5'>
                        <label for='pesquisa'></label>
                        <input class='form-control' type='text' name='pesquisa' id='pesquisa' placeholder='Apenas digite o código de evento!' maxlength='4'><img src='imagens/search.png'>
                    </div>
                </div>
                
            </form>
            
            </div>
            </div>
            
            <ul class='resultado'>
            
            </ul>
			
		";

        ?>

     <?php }


        /*************************************************************************************
         *      SELECIONAR TODOS OS PARTICIPANTES COM DADOS INVÁLIDOS NA TABELA WEBNINAR_2   *
         * ***********************************************************************************/

        if ((isset($_POST["hdAcao"])) && ($_POST["hdAcao"] == "Problemas")) {

            echo" <div class='container'>
    
        <div id='buscar'>

            <p id='pesquisar_codEvento'>Pesquisar Código do Evento</p>
        
            <form method='POST' id='form-pesquisa' action=''>
                <div class='row'>
                    <div class='col-md-5'>
                        <label for='pesquisa'></label>
                        <input class='form-control' type='text' name='pesquisa' id='pesquisa' placeholder='Apenas digite o código de evento!' maxlength='4'><img src='imagens/search.png'>
                    </div>
                </div>
                
            </form>
            
            </div>
            </div>
            
            <ul class='resultado'>
            
            </ul>
			
		";



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

                        <label for="codInscricao">Data de cadastro:</label>
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

