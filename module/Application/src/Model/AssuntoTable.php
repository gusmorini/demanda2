<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class AssuntoTable
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;
    
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function persist(Assunto $assunto)
    {
        $set = $assunto->toArray();
        $result = $this->tableGateway->select(['assunto' => $set['assunto']]);
        // if ($result->count() == 0){
        //     $this->tableGateway->insert($set);
        // } 
        if ($result->count() > 0) {
            return false;
        } else {
            $this->tableGateway->insert($set);
            return true;
        }

    }

    public function getMaxCodigo()
    {
        $expression = new Expression('max(codigo)');
        $select = new Select('assunto');
        $select->columns(['codigoAssunto' => $expression]);
        return $this->tableGateway->selectWith($select)->current()['codigoAssunto'];
    }

       
}