<?php


$nome = "Dan'ie'l";
$sobrenome = 'H"enri"que';

$nomeCompleto = $nome . " " . $sobrenome;

$nomeCompletoVerficado = str_replace("'","",(str_replace('"',"", $nomeCompleto)));

echo $nomeCompletoVerficado . "<br>";



echo  $cpf2 = "05775727707 <br>";

$cpf = str_pad($cpf2,11,'0', STR_PAD_LEFT);

$teste =  "antonio baptista valentim varella junior baptista valentim varella junior";

//echo strlen($teste);


$nomecomaspas = "A'DRIANA BARBO'SA MASCARENHAS'";
echo "<br>";

 echo $teste01 = addslashes(substr($nomecomaspas, -1));

echo "<br>";
 if($teste01 == "\'"){
     echo "verdadeiro";
     $nomecomaspas = $nomecomaspas . "";
 }else {
     echo "Falso";
 }




function validaCPF($cpf) {

    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return $cpf . "-" . false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return $cpf . "-" . false;
    }

    // Faz o calculo para validar o CPF
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

$teste = "";

$teste = explode("-",validaCPF($cpf));

echo $teste[0] . "<br>";
echo $teste[1] . "<br>";

if($teste[1] == 1){
    echo "CPF válido";
}

else if ($teste[1] != 1 ){
    echo "CPF inválido";
}

$nome = "Motorola";

function nomeAparelhoCelular($nome)
{
    $verificarNomeInvalido = strtolower($nome);
    $nomeCelular = '';
    $nomeCelulares = array('asus', 'galaxy', 'samsung', 'motorola');

    foreach ($nomeCelulares as $value) {
        $pos = strpos($verificarNomeInvalido, $value);

        if (!($pos === false))
            $nomeCelular = $nomeCelular . $value . '|';
    }

    if (strlen($nomeCelular) > 1) {
        return $nome . "-" . false;
    } else {
        return $nome . "-" . true;
    }

}

$nomeVerificadoInvalido = explode("-",nomeAparelhoCelular($nome));

echo "<br>" . $nomeVerificadoInvalido[0] . "<br>";
echo $nomeVerificadoInvalido[1];
