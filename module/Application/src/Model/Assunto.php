<?php

namespace Application\Model;

class Assunto
{
    public $assunto;
    public $detalhes;

    public function __construct(array $data){
        $this->assunto = $data['assunto'] ?? null;
        $this->detalhes = $data['detalhes'] ?? null;
    }    
    
    public function toArray()
    {
        return get_object_vars($this);
    }
}