<?php

    namespace Application\Controller;

    use Interop\Container\ContainerInterface;
    use Zend\ServiceManager\Factory\FactoryInterface;
    use Interop\Container\Exception\ContainerException;
    use Zend\ServiceManager\Exception\ServiceNotCreatedException;
    use Zend\ServiceManager\Exception\ServiceNotFoundException;
    use Zend\Session\SessionManager;
    


    class IndexControllerFactory implements FactoryInterface
    {
        public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL)
        {
            $sessionManager = new SessionManager();
            $sessionManager->start();
            return new IndexController($container);
        }
    }

