<?php

require_once ('eventoCSV.php');
require_once ('conexao.php');

class CSV_DAO {


    // INSERÇÃO DE REGISTRO VÁLIDOS
    public function insertWebinar (eventoCSV $e){

        try{

            Conexao::getConn()->beginTransaction();
            $sql = "INSERT INTO webinar (nome,email,cpf,profissao,insert_data, codEvento) VALUES (?,?,?,?,?,?)";
            $stmt = Conexao::getConn()->prepare($sql);

            $stmt->bindValue(1,$e->getNome(), PDO::PARAM_STR);
            $stmt->bindValue(2,$e->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(3,$e->getCpf(), PDO::PARAM_INT);
            $stmt->bindValue(4,$e->getProfissao(), PDO::PARAM_STR);
            $stmt->bindValue(5,$e->getDataAtual(), PDO::PARAM_STR);
            $stmt->bindValue(6,$e->getCodEvento(), PDO::PARAM_INT);

            $stmt->execute();

            if(!$stmt):
                Conexao::getConn()->rollBack();
                die("Erro no insert");
            endif;

            Conexao::getConn()->commit();

        }catch (PDOException $exception){
            $exception->getMessage();
            Conexao::getConn()->rollBack();
        }
    }

    // INSERÇÃO DE REGISTROS INVÁLIDOS
    public function insertWebinarInvalido (eventoCSV $e){

        try{
            Conexao::getConn()->beginTransaction();
            $sql = "INSERT INTO webinar_invalidos (nome,email,cpf,profissao,obs,insert_data, codEvento) VALUES (?,?,?,?,?,?,?)";
            $stmt = Conexao::getConn()->prepare($sql);

            $stmt->bindValue(1,$e->getNome(), PDO::PARAM_STR);
            $stmt->bindValue(2,$e->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(3,$e->getCpf());
            $stmt->bindValue(4,$e->getProfissao(), PDO::PARAM_STR);
            $stmt->bindValue(5,$e->getObs(), PDO::PARAM_STR);
            $stmt->bindValue(6,$e->getDataAtual(), PDO::PARAM_STR);
            $stmt->bindValue(7,$e->getCodEvento(), PDO::PARAM_INT);

            $stmt->execute();

            if(!$stmt):
                Conexao::getConn()->rollBack();
                die("Erro no insert");
            endif;

            Conexao::getConn()->commit();

        }catch (PDOException $exception){
            $exception->getMessage();
            Conexao::getConn()->rollBack();
        }

    }

    // CASO O ARQUIVO SEJA INVÁLIDO, VAI DELETAR OS REGISTROS
    public function DeleteWebinarInvalidos(eventoCSV $e){

        try{

            $sql = "DELETE FROM webinar_invalidos WHERE insert_data=? AND codEvento=?";
            $stmt = Conexao::getConn()->prepare($sql);

            $stmt->bindValue(1, $e->getDataAtual());
            $stmt->bindValue(2, $e->getCodEvento());

            $stmt->execute();


        }catch (PDOException $exception){
            $exception->getMessage();
        }
    }

    // CASO O ARQUIVO SEJA INVÁLIDO, VAI DELETAR OS REGISTROS
    public function DeleteWebinar(eventoCSV $e){

        try{

            $sql = "DELETE FROM webinar WHERE insert_data=? AND codEvento=?";
            $stmt = Conexao::getConn()->prepare($sql);

            $stmt->bindValue(1, $e->getDataAtual());
            $stmt->bindValue(2, $e->getCodEvento());

            $stmt->execute();


        }catch (PDOException $exception){
            $exception->getMessage();
        }
    }

    // CONSULTAR NOVOS PARTICIPANTES QUE NÃO ESTÃO CADASTRADO NO SISTEMA
    public function ConsultarParticipantes(){

        $sql = "SELECT distinct p.nome, p.cpf, p.email from webinar p where cpf not in (select cpf from participante) group by cpf  ORDER BY `nome` ASC ";
        $stmt = Conexao::getConn()->prepare($sql);

        $stmt->execute();

        if( $stmt->rowCount() > 0):
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        else:
            echo "<div class='alert alert-success' role='alert'>
                       Não há participantes novos!
                    </div>";
            return [];
        endif;
    }

    public function InsertParticipante($dados = array()){

    try{
        Conexao::getConn()->beginTransaction();
        $sql = "INSERT INTO participante (nome,cpf, email, senha) VALUES (?,?,?,?)";
        $stmt = Conexao::getConn()->prepare($sql);

        $rows = 0;

        foreach ($dados as $value):

            $resultValue = explode(',',$value);

            $stmt->bindValue(1,$resultValue[0],PDO::PARAM_STR);
            $stmt->bindValue(2,$resultValue[1]);
            $stmt->bindValue(3,$resultValue[2],PDO::PARAM_STR);
            $stmt->bindValue(4,'202cb962ac59075b964b07152d234b70',PDO::PARAM_STR);

            $stmt->execute();

            if(!$stmt):
                Conexao::getConn()->rollBack();
                die("Erro no insert");
            endif;

            Conexao::getConn()->commit();

            $rows++;

        endforeach;

        if ($stmt->rowCount() > 0):
            echo "<div class='alert alert-success col-md-6 col-xs-8' style='font-size: 1.2em; margin-top: 50px;'> <strong>" . $rows . "</strong> Participantes inseridos na Tabelas Participantes com Sucesso! </div>";
            echo'<input style="margin-top: 140px; margin-left: -80px; padding: 10px" name="Voltar" type="button" value="VOLTAR" class="btn btn-primary" onClick="history.go(-2)"; >';

        else:
            echo "<script>alert('Erro ao Inserir Participante');</script>";
        endif;

        }catch (PDOException $exception){
            $exception->getMessage();
            Conexao::getConn()->rollBack();
      }
    }

    // Consultar participantes webinar, que não tem inscrições
    public function consultaInscricao($codEvento, $dataInvertida){

        $retornaAnoInvertida = substr($dataInvertida, -4);
        $retornaMesInvertida = substr($dataInvertida, 2, -4);
        $retornaDiaInvertida = substr($dataInvertida, 0, 2);
        $retornaDataInvertida = $retornaAnoInvertida . $retornaMesInvertida . $retornaDiaInvertida;
        $dataInsert = str_replace("/","",$retornaDataInvertida);

        try {
            $sql = "SELECT DISTINCT  p.nome, p.cpf, p.email, w.codEvento, p.codigo FROM participante p, webinar w WHERE p.cpf=w.cpf AND w.codEvento = ? AND insert_data = ? order by p.nome";
            $stmt = Conexao::getConn()->prepare($sql);

            $stmt->bindValue(1, $codEvento, PDO::PARAM_INT );
            $stmt->bindValue(2, $dataInsert, PDO::PARAM_STR);

            $stmt->execute();

            if( $stmt->rowCount() == 0):
                $result = ['erro'];
                return $result;
            else:
                $result = $stmt->fetchAll(\PDO::FETCH_NUM);
                return $result;
            endif;

        } catch (PDOException $exception){
            echo "Erro" . $exception->getMessage();
        }
    }

    public function  insertInscricao($dados = array()){

        $dataAtual = new DateTime('now');
        $dataString = $dataAtual ->format("Y-m-d H:i:s");

        $k = 0;
        $l = 0;

        try {

            Conexao::getConn()->beginTransaction();

            $sqlValida = "SELECT i.codEvento, i.codParticipante FROM inscricoes i WHERE ((i.codEvento = ?) AND (i.codParticipante = ?))";
            $stmt = Conexao::getConn()->prepare($sqlValida);

            $sqlInsert = "INSERT INTO inscricoes (codEvento, codParticipante, impedido, observacao, dataHoraInscricao) VALUES (?,?,?,?,?)";
            $stmt2 = Conexao::getConn()->prepare($sqlInsert);


            foreach ($dados as $value):

                $resultValue = explode(',',$value);

                $stmt->bindValue(1,$resultValue[3], PDO::PARAM_INT);
                $stmt->bindValue(2,$resultValue[4], PDO::PARAM_INT);

                $stmt->execute();

                if(!$stmt):
                    Conexao::getConn()->rollBack();
                    die("Erro na consulta");
                endif;

                if($stmt->rowCount() == 1):
                    $l++;
                else:
                    $stmt2->bindValue(1, $resultValue[3], PDO::PARAM_INT);
                    $stmt2->bindValue(2, $resultValue[4], PDO::PARAM_INT);
                    $stmt2->bindValue(3, 0, PDO::PARAM_INT);
                    $stmt2->bindValue(4, ' ', PDO::PARAM_STR);
                    $stmt2->bindValue(5, $dataString, PDO::PARAM_STR);

                    $k++;

                    $stmt2->execute();

                    if(!$stmt2):
                        Conexao::getConn()->rollBack();
                        die("Erro no insert");
                    endif;

                endif;
            endforeach;

            if ($stmt2->rowCount() == 0 && $stmt->rowCount() == 0):
                echo "<script>alert('Erro na operação, tente novamente!');</script>";

            else:
                echo "<div class='container'><div class='alert alert-success col-md-6 col-xs-8' style='font-size: 1.2em;'> <strong>" . $k . "</strong> Presenças realizadas com Sucesso! </div></div>";
                echo "<br>";
                echo "<div class='container'><div class='alert alert-danger col-md-6 col-xs-8' style='font-size: 1.2em;'> <strong>" . $l . "</strong> Presenças já constava na Tabela de Inscrição! </div></div>";
                echo"<input style='margin-left: 15px;' name='Voltar' type='button' value='Voltar Página Anterior' class='btn btn-primary' onClick='history.go(-2);' >";

            endif;

            Conexao::getConn()->commit();

        }catch (PDOException $exception){
            $exception->getMessage();
            Conexao::getConn()->rollBack();
        }
    }
}