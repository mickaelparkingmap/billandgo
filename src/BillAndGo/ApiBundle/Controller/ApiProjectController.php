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
use BillAndGo\ApiBundle\Entity\AccessToken;
use BillAndGo\ApiBundle\Service\AuthentificationService;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiProjectController extends Controller
{

    /** @var AuthentificationService */
    private $authService;
    /** @var ProjectService */
    private $projectService;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->authService = new AuthentificationService($this->getDoctrine()->getRepository(AccessToken::class));
        $this->projectService = $this->get("AppBundle\Service\ProjectService");
    }

    /**
     * @return Response
     * @Get("/api/project")
     */
    public function getApiProjectIndexAction () : Response
    {
        $user = $this->authService->authenticate();
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
    public function getApiProjectAction (int $id) : Response
    {
        $user = $this->authService->authenticate();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $client = $this->projectService->getProject($user, $id);
            $response = new Response(Serializer::serialize($client));
        }
        return $response;
    }


}