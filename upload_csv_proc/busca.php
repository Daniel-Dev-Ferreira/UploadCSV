<?php
	//Incluir a conexão com banco de dados
	include_once('conexao.php');
	
	//Recuperar o valor da palavra
	$codEvento = $_POST['palavra'];
	
	//Pesquisar no banco de dados nome do curso referente a palavra digitada pelo usuário
$queryTodos = "SELECT DISTINCT(cpf), nome, email, codEvento FROM webinar WHERE codEvento LIKE '%$codEvento%'";
$result = mysqli_query($conn, $queryTodos);
$totalrow = mysqli_num_rows($result);

echo("<div class='container'>

                    <h2>Dados de Todos os Participantes</h2>
                        <table class='table table-striped'>
        
                    <tr style='background-color:#337AB7; color:white;'>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Código Evento</th>
                    </tr>");


	if(mysqli_num_rows($result) <= 0){
		echo "Nenhum curso encontrado...";
	}else{
        while($result_row = mysqli_fetch_row($result)){

            echo("<tr>
                           
                           <td>$result_row[1]</td>
                           <td>$result_row[0]</td>
                           <td>$result_row[2]</td>
                           <td>$result_row[3]</td>
                      </tr>");

        }

        echo"<tr>
        <th colspan='1' class='TDtable1'style='background-color:#337AB7; color:white; text-align: center' >Total de Registros</th>
        <th colspan='3' class='TDtable1 bg-success' style='text-align: center' > $totalrow </th>
    </tr>

    </table></div>";

	}
?>


