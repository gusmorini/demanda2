<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

// config do processarAction
use Zend\ServiceManager\ServiceManager;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

// fabrica???
use Interop\Container\ContainerInterface;

use Application\Model\Solicitante;
use Application\Model\SolicitanteTable;

use Application\Model\Assunto;
use Application\Model\AssuntoTable;

use Application\Model\Demanda;
use Application\Model\DemandaTable;


//require "vendor/autoload.php";

class IndexController extends AbstractActionController
{
    // tipo @var ContainerInterface
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    // Action é uma convernção do framework
    public function processarAction() {

        $solicitante = new Solicitante($_POST);
        $assunto = new Assunto($_POST);

        $_SESSION['dados'] = [
            'solicitante' => $solicitante,
            'assunto' => $assunto,
        ];

        if (!$solicitante->cpf || !$solicitante->nome || !$assunto->assunto || !$assunto->detalhes) {
            $_SESSION['dados']['msg'] = "Preencha todos os campos obrigatórios * ";
            return $this->redirect()->toRoute('application');
        }

        //configuração do modulo
        $solicitanteTable = $this->container->get('SolicitanteTable');

        if (!$solicitanteTable->persist($solicitante)) {
            $_SESSION['dados']['msg'] = "CPF já existe";
            return $this->redirect()->toRoute('application');
        }

        $assuntoTable = $this->container->get('AssuntoTable');

        if (!$assuntoTable->persist($assunto)) {
            $_SESSION['dados']['msg'] = "Assunto já existe";
            return $this->redirect()->toRoute('application');
        }

        $codigoAssunto = $assuntoTable->getMaxCodigo();

        $data = array ('codigo_solicitante' => $solicitante->cpf, 'codigo_assunto' => $codigoAssunto);

        $demanda = new Demanda($data);     
        $demandaTable = $this->container->get('DemandaTable');        
        $demandaTable->persist($demanda);

        $_SESSION['dados'] = [];
        $_SESSION['dados']['msg'] = "Cadastro Realizado";
        return $this->redirect()->toRoute('application');

        exit;

        function bancoInsert ($tabela, $coluna, $valor, $adapter) {
            $insert = new Insert($tabela);
            $insert->columns($coluna)->values($valor);
            $sql = $insert->getSqlString($adapter->getPlatform());
            $statement = $adapter->query($sql);
            //$insertedRows = $statement->execute();
        }

        $select = new Select('solicitante');
        $select->columns(['cpf'])->where(['cpf'=>$cpf]);
        $sql = $select->getSqlString($adapter->getPlatform());
        $statement = $adapter->query($sql);
        $res1 = $statement->execute();

        $select = new Select('assunto');
        $select->columns(['assunto'])->where(['assunto'=>$assunto]);
        $sql = $select->getSqlString($adapter->getPlatform());
        $statement = $adapter->query($sql);
        $res2 = $statement->execute();

        if ((count($res1) > 0) OR (count($res2) > 0)) {

            $_SESSION['dados']['msg'] = (count($res1) > 0 ? "CPF já cadastrado" : "Assunto duplicado");

            return $this->redirect()->toRoute('application');

        } else {

            $tabela = 'solicitante';
            $coluna = ['cpf','nome','CEP','municipio','UF','email','ddd','telefone'];
            $valor = [$cpf,$nome,$cep,$municipio,$uf,$email,$ddd,$telefone];
            bancoInsert($tabela, $coluna, $valor, $adapter);

            $tabela = 'assunto';
            $coluna = ['assunto','detalhes'];
            $valor = [$assunto, $detalhes];
            bancoInsert($tabela, $coluna, $valor, $adapter);

            $expression = new Expression('max(codigo)');
            $select = new Select('assunto');
            $select->columns(['codigoAssunto' => $expression]);
            $sql = $select->getSqlString($adapter->getPlatform());
            $statement = $adapter->query($sql);
            $result = $statement->execute();
            $codigo_assunto = $result->current()['codigoAssunto'];

            //fecha a conexão 
            $adapter->getDriver()->getConnection()->disconnect();

            $tabela = 'demanda';
            $coluna = ['codigo_solicitante','codigo_assunto'];
            $valor = [$cpf, $codigo_assunto];
            bancoInsert($tabela, $coluna, $valor, $adapter);
            
        }

        //return new ViewModel(array('msg' => $msg));


    }
}
