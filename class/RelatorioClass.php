<?php

date_default_timezone_set('america/sao_paulo');

require_once 'RamaisClass.php';

class RelatorioClass extends Ramais
{
    private $fileCsv;
    private $fileName = 'dadosRamais.csv';

    public function __construct()
    {
        parent::__construct(file('../lib/ramais'), file('../lib/filas'));
    }

    public function gerarRelatorio()
    {
        $data = $this->dataCsv();
        $file = fopen($this->fileName, "w");

        foreach ($data as $linha) {
            fputcsv($file, $linha, ';');
        }

        fclose($file);

        return $this->fileCsv = file_get_contents($this->fileName);
    }

    private function dataCsv() : array
    {
        $headerCsv = ['nome', 'ramal', 'ip', 'online', 'status', 'agente', 'data'];
        $data[] = $headerCsv;
        $ramais = $this->getInfoRamais();

        $ramais = json_decode($ramais, true);

        foreach ($ramais as $index) {
            $dadosRamias = [ $index['nome'], $index['ramal'], $index['ip'], $index['online'] == 1 ? 'true' : 'false', $index['status'], $index['agente'], $this->data()];
            $data[] = $dadosRamias;
        }

        return $data;

    }

    private function data()
    {
        return (new DateTime())->format('d-m-Y H:i:s');
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
