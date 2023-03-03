<?php

require_once 'RamaisClass.php';

class RamaisDatabaseClass extends Ramais {

    private $conn;

    public function __construct($connect)
    {
        parent::__construct(file('../lib/ramais'), file('../lib/filas'));
        $this->conn = $connect;
    }

    public function updateData() : bool
    {
        $listaRamais = $this->getInfoRamais();
        $ramais = json_decode($listaRamais, true);

        foreach ($ramais as $ramal) {
           
            if ($this->issetRamal($ramal['nome'])) {
                $this->updateRamal($ramal);
            } else {
                $this->createRamal($ramal);
            }

        }
        
        $this->removeRamaisInexistentesDB($ramais);

        return true;
    }

    private function createRamal(Array $ramal) : bool
    {
        
        $data = [
            'nome'   => $ramal['nome'],
            'ramal'  => $ramal['ramal'],
            'agente' => $ramal['agente'],
            'ip'     => $ramal['ip'],
            'online' => $ramal['online'] === false ? 0 : 1,
            'statusGrupo' => $ramal['status'],
        ];

        $colunas = implode(", ", array_keys($data));
        $values = implode("', '", array_values($data));

        $sql = "INSERT INTO ramais ($colunas) VALUES ('$values')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private function updateRamal(Array $ramal) : bool
    {
        $data = [
            'nome'   => $ramal['nome'],
            'ramal'  => $ramal['ramal'],
            'agente' => $ramal['agente'],
            'ip'     => $ramal['ip'],
            'online' => $ramal['online'] === false ? 0 : 1,
            'statusGrupo' => $ramal['status'],
        ];

        $sql = "UPDATE ramais SET ";

        foreach ($data as $coluna => $value) {
            $sql .= "$coluna = '$value', ";
        }

        $sql = rtrim($sql, ", ");
        $nomeRamal= $data['nome'];
        $sql .= " WHERE nome = '$nomeRamal'";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private function issetRamal($nome) : bool
    {
        $sql = "SELECT * FROM ramais WHERE nome = '{$nome}'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $resultados = $stmt->rowCount();

        return $resultados > 0;
    }

    private function removeRamaisInexistentesDB(Array $ramaisExistentes) : bool
    {
        $nomesramais = [];

        foreach ($ramaisExistentes as $value) {
            $nomesramais[] = $value['nome'];
        }

        $query = implode(", ", array_values($nomesramais));
        $sql = "DELETE FROM ramais WHERE ramal NOT IN ($query)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return true;
    }

}

