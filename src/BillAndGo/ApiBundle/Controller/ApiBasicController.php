<?php

/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */

namespace BillAndGo\ApiBundle\Controller;


use BillAndGo\ApiBundle\Entity\AccessToken;
use BillAndGo\ApiBundle\Service\AuthentificationService;
use BillAndGoBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApiBasicController extends FOSRestController
{
    /** @var AuthentificationService */
    private $authService;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer (ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->authService = new AuthentificationService($this->getDoctrine()->getRepository(AccessToken::class));
    }

    public function getUser () : User
    {
        return $this->authService->authenticate();
    }

}