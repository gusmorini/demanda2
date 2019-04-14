<?php

namespace Application\Model;

class Demanda
{
    // @var integer
    //public $codigo;

    // @var Assunto
    //public $assunto;

    // @var Solicitante
    //public $solicitante;

    public $codigo_solicitante;
    public $codigo_assunto;
    

    public function __construct(array $data){
        $this->codigo_solicitante = $data['codigo_solicitante'] ?? null;
        $this->codigo_assunto = $data['codigo_assunto'] ?? null;
    }    
    
    public function toArray()
    {
        return get_object_vars($this);
    }
}