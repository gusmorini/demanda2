<?php
namespace Application\Model;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Db\TableGateway\TableGateway;

class DemandaTableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, 
        $requestedName, array $options = null)
    {
        $adapter = $container->get('DbAdapter');
        $tableGateway = new TableGateway('demanda', $adapter);
        return new DemandaTable($tableGateway);   
    }
}