<?php

/**
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 */

namespace BillAndGo\ApiBundle\Controller;


use AppBundle\Service\ProjectService;
use AppBundle\Service\Serializer;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiProjectController extends ApiClientController
{
    /** @var ProjectService */
    private $projectService;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->projectService = $this->get("AppBundle\Service\ProjectService");
    }

    /**
     * @return Response
     * @Get("/api/project")
     */
    public function getApiProjectIndexAction () : Response
    {
        $user = $this->getUser();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $list = $this->projectService->getProjectList($user);
            $response = new Response(Serializer::serialize($list));
        }
        return $response;
    }

    /**
     * @return Response
     *
     * @Get("/api/project/{id}")
     */
    public function getApiProjectAction (int $projectID) : Response
    {
        $user = $this->getUser();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $client = $this->projectService->getProject($user, $projectID);
            $response = new Response(Serializer::serialize($client));
        }
        return $response;
    }


}