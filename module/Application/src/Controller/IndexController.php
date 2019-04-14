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
// use Zend\ServiceManager\ServiceManager;
// use Zend\Db\Sql\Insert;
// use Zend\Db\Sql\Select;
// use Zend\Db\Sql\Expression;

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
    }
}
