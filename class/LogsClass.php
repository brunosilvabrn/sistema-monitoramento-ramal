<?php

date_default_timezone_set('america/sao_paulo');

require_once 'RamaisClass.php';

class LogsClass extends Ramais
{
    private $conn;
    private $fileCsv;
    private $fileName = 'Logs.csv';

    public function __construct($connect)
    {
        parent::__construct(file('../lib/ramais'), file('../lib/filas'));
        $this->conn = $connect;
    }

    public function registerLog() : bool
    {
        $ramais = json_decode($this->getInfoRamais(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($ramais as $ramal)
        {
            $status = $this->getStatusAntigo($ramal['ramal']) === '' ? '*' : $this->getStatusAntigo($ramal['ramal']) ;

            if ($this->verifyRamal($ramal['ramal'], $ramal['status'])) {
                $this->inserirlog($ramal['ramal'], $status, $ramal['status']);
            }
        }

        return true;
    }

    public function inserirlog(String $ramal, String $statusAntigo, String $statusNovo) : bool {
        $data = $this->dataHora();
        $sql = "INSERT INTO log_ramais (ramal, status_antigo, status_novo, data) 
                VALUES ('$ramal', '$statusAntigo', '$statusNovo', '$data')";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private function getStatusAntigo(String $ramal)
    {
        $sql = "SELECT * FROM log_ramais WHERE ramal = '$ramal' ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dados['status_novo'] ?? '';
    }

    public function verifyRamal(String $ramal, String $statusAtual)
    {
        $sql = "SELECT * FROM log_ramais WHERE ramal = '$ramal' ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        $status = $dados['status_novo'] ?? '';

        return !($status === $statusAtual);
    }

    public function getLogsDB()
    {
        $sql = "SELECT * FROM log_ramais ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function relatorioLogs()
    {
        $sql = "SELECT log_ramais.*, ramais.nome as nome_ramal, ramais.agente, ramais.ip, ramais.online 
          FROM log_ramais 
          LEFT JOIN ramais ON log_ramais.ramal = ramais.ramal";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->dataLogCSV($dados);
    }

    public function gerarRelatorio()
    {
        $data = $this->relatorioLogs();
        $file = fopen($this->fileName, "w");

        foreach ($data as $linha) {
            fputcsv($file, $linha, ';');
        }

        fclose($file);

        return $this->fileCsv = file_get_contents($this->fileName);
    }

    private function dataLogCSV(Array $ramais)
    {
        $headerCsv = ['id', 'nome', 'ramal', 'ip', 'status_atingo', 'status_novo', 'data'];
        $data[] = $headerCsv;

        foreach ($ramais as $index) {
            $dadosRamias = [$index['id'], $index['nome_ramal'], $index['ramal'], $index['ip'], $index['status_antigo'], $index['status_novo'], $index['data']];
            $data[] = $dadosRamias;
        }

        return $data;
    }

    private function dataHora()
    {
        return (new DateTime())->format('Y-m-d h:i:s');
    }

    private function deleteCsv()
    {
        if ($this->fileCsv !== null)
        {
            unlink('src/'. $this->fileName);
        }
    }

    public function __destruct()
    {
        $this->deleteCsv();
    }
}