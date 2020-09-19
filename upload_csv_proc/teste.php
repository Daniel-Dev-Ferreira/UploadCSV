<?php

// teste depois para tira duplicidade do arquivo CSV
/*
function getCSV($name) {
$file = fopen($name, "r");
$result = array();
$i = 0;
while (!feof($file)):
if (substr(($result[$i] = fgets($file)), 0, 10) !== ';;;;;;;;') :
$i++;
endif;
endwhile;
fclose($file);
return $result;
}

function getLine($array, $index) {
return explode(';', $array[$index]);
}

$foo = getCSV('foo.csv');
$foo = array_unique($foo); // remove os repetidos
for ($i = 0; $i < count($foo); $i++) {
$line = getLine($foo, $i);
if (!empty($line[0]) && trim($line[1]) == 's') { // válida se tem valor e é ativo.
print_r($line); // faça os insert's aqui
}
}
*/



?>

<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

    <body>
    <select class="cadastroDireitaInput cadastroSiteSexo" id="sexo" name="sexo">
        <option value="false">Selecione</option>
        <option value="Feminino">Feminino</option>
        <option value="Masculino">Masculino</option>
    </select>
    <div id="resultado"></div>

    <script>

        $('#sexo').on('change', function(){
            var valor = (!!this.value + ' ' + this.value);

            if (valor == "false"){
                alert("teste");
            }else {
                alert ("teste2");
            }
        });




    </script>
    </body>
</html>
