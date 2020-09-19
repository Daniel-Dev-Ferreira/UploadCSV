<?php



class Conexao{

    private static $instance;

    public static function getConn(){

        try {

            if (!isset(self::$instance)):
                self::$instance = new \PDO("mysql:host=localhost;dbname=emerjco_eventos;charset=utf8", "root", "");

            endif;

            return self::$instance;

        }catch (mysqli_sql_exception $e) {

            die ("erro ao criar conexao:".$e->errorMessage());

        }
    }
}