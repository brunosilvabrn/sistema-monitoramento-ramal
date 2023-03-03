<?php

header("Content-type: application/json; charset=utf-8");

class Ramais {

    private $ramais;
    private $filas;

    public $statusRamais = [
        '(Ring)'        => 'chamando',
        '(In use)'      => 'ocupado',
        '(Not in use)'  => 'disponivel',
        '(Unavailable)' => 'indisponivel',
        '(paused)'      => 'pausado'
    ];

    public function __construct($ramais, $filas)
    {
        $this->ramais = $ramais;
        $this->filas = $filas;
    }

    private function addStatusRamais() : array
    {
        $filas = $this->filas;
        $status_ramais = array();

        foreach($filas as $linhas){
            if(strpos($linhas, 'SIP/') !== false){
                foreach ($this->statusRamais as $key => $value) {
                    if(strpos($linhas, $key) !== false){
                        $linha = explode(' ', trim($linhas));
                        $agente = $linha[count($linha) - 1];
                        list($tech,$ramal) = explode('/',$linha[0]);
                        $status_ramais[$ramal] = array(
                            'status' => $value,
                            'agente' => $agente,
                        );
                    }
                }
            }
        }

        return $status_ramais;

    }

    public function getInfoRamais() : string
    {
        $info_ramais = array();

        foreach($this->ramais as $linhas){

            $linha = array_filter(explode(' ',$linhas));
            $arr = array_values($linha);
            $status_ramais = $this->addStatusRamais();

            if (preg_match('/^\d+\/\d+$/', $arr[0])) {

                $ip = $linha[18];
                $online = !((trim($arr[1]) === '(Unspecified)' && trim($arr[4]) === 'UNKNOWN') ||
                    (count($arr) < 6 || trim($arr[5]) !== "OK"));

                list($name,$username) = explode('/',$arr[0]);
                $info_ramais[$name] = array(
                    'nome' => $username,
                    'ramal' => $name,
                    'ip'    => $ip,
                    'online' => $online,
                    'status' => $status_ramais[$name]['status'],
                    'agente' => $status_ramais[$name]['agente']
                );
            }
        }

        return json_encode($info_ramais);
    }

    public function searchRamais(String $status, Bool $busca = false) : string
    {
        $allRamais = json_decode($this->getInfoRamais(), true, 512, JSON_THROW_ON_ERROR);
        $ramaisSearch = [];

        if ($busca === true) {
            foreach($allRamais as $ramal){
                if($ramal['nome'] === $status || $ramal['ramal'] === $status || strtolower($ramal['agente']) === strtolower($status)) {
                    $ramaisSearch[] = $ramal;
                }
            }

            return json_encode($ramaisSearch);
        }

        foreach($allRamais as $ramal){
            if($ramal['status'] === $status) {
                $ramaisSearch[] = $ramal;
            }
        }

        return json_encode($ramaisSearch);
    }

    public function getQuantidadesStatusRamais() : string
    {
        $statusPermitidos = ['indisponivel', 'disponivel', 'chamando', 'pausado', 'ocupado'];
        $quantidades = array_fill_keys($statusPermitidos, 0);

        $ramais = json_decode($this->getInfoRamais(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($ramais as $ramal) {
            if (in_array($ramal['status'], $statusPermitidos)) {
                ++$quantidades[$ramal['status']];
            }
        }

        return json_encode($quantidades);
    }

}
