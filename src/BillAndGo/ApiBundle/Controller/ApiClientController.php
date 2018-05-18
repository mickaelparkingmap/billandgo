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


use AppBundle\Service\ClientService;
use AppBundle\Service\Serializer;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiClientController extends ApiBasicController
{
    /** @var ClientService */
    private $clientService;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->clientService = $this->get("AppBundle\Service\ClientService");
    }

    /**
     * @return Response
     * @Get("/api/client")
     */
    public function getApiClientIndexAction () : Response
    {
        $user = $this->getUser();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $list = $this->clientService->getClientListFromUser($user);
            $response = new Response(Serializer::serialize($list));
        }
        return $response;
    }

    /**
     * @return Response
     *
     * @Get("/api/client/{id}")
     */
    public function getApiClientAction (int $clientID) : Response
    {
        $user = $this->getUser();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $client = $this->clientService->getClient($user, $clientID);
            $response = new Response(Serializer::serialize($client));
        }
        return $response;
    }

}