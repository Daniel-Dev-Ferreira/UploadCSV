<?php

	$servidor = "localhost";

	$usuario = "root";

	$senha = "";

	$dbname = "emerjco_eventos";

	try {

			//Criar a conexao

	$conn = mysqli_connect($servidor, $usuario, $senha, $dbname);



	$conn->set_charset("utf8");




	}catch (mysqli_sql_exception $e) { 

			die ("erro ao criar conexao:".$e->errorMessage());

	}



		//echo "Conexao realizada com sucesso";



	

?>