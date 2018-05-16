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

use AppBundle\Service\Serializer;
use BillAndGo\ApiBundle\Entity\AccessToken;
use BillAndGo\ApiBundle\Service\AuthentificationService;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiProfileController extends FOSRestController
{
    /** @var AuthentificationService */
    private $authService;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->authService = new AuthentificationService($this->getDoctrine()->getRepository(AccessToken::class));
    }

    /**
     * @return Response
     *
     * @Get("/api/profile")
     */
    public function getProfileAction () : Response
    {
        $user = $this->authService->authenticate();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $response = new Response(Serializer::serialize($user));
        }
        return $response;
    }


}