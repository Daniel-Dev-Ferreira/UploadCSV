<?php

require_once ('CSV_DAO.php');

class eventoCSV {

    protected $nome;
    protected $email;
    protected $cpf;
    protected $profissao;
    protected $dataAtual;
    protected $codEvento;
    protected $obs;

    public function __construct( $nome, $email, $cpf, $profissao, $dataAtual, $codEvento){
        $this ->nome = $nome;
        $this ->email = $email;
        $this ->cpf = $cpf;
        $this ->profissao = $profissao;
        $this ->dataAtual = $dataAtual;
        $this ->codEvento = $codEvento;
        $this ->obs = "";
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @return mixed
     */
    public function getSobrenome()
    {
        return $this->sobrenome;
    }

    /**
     * @return mixed
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @return mixed
     */
    public function getProfissao()
    {
        return $this->profissao;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getDataAtual()
    {
        return $this->dataAtual;
    }

    /**
     * @return mixed
     */
    public function getCodEvento()
    {
        return $this->codEvento;
    }

    /**
     * @return mixed
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * @param string $obs
     */
    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    /**
     * @param mixed $cpf
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
    }

    function nomeAparelhoCelular($nomeCompletoVerficado) {

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

}

