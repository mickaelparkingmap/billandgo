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

use AppBundle\Service\DocumentService;
use AppBundle\Service\Serializer;
use BillAndGo\ApiBundle\Entity\AccessToken;
use BillAndGo\ApiBundle\Service\AuthentificationService;
use BillAndGoBundle\Entity\Document;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiDevisController
 * @package BillAndGo\ApiBundle\Controller
 */
class ApiDocumentController extends FOSRestController
{
    /**
     * @return Response
     *
     * @Get("/api/estimate")
     */
    public function getApiEstimateIndexAction () : Response
    {

        $authService = new AuthentificationService($this->getDoctrine()->getRepository(AccessToken::class));
        $user = $authService->authenticate();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            /** @var DocumentService $documentService */
            $documentService = $this->get("AppBundle\Service\DocumentService");
            $list = $documentService->listDrawFromUser($user);
            $response = new Response(Serializer::serialize($list));
        }
        return $response;
    }

    /**
     * @return Response
     *
     * @Get("/api/bill")
     */
    public function getApiBillIndexAction () : Response
    {
        $authService = new AuthentificationService($this->getDoctrine()->getRepository(AccessToken::class));
        $user = $authService->authenticate();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            /** @var DocumentService $documentService */
            $documentService = $this->get("AppBundle\Service\DocumentService");
            $list = $documentService->listBillsFromUser($user);
            $response = new Response(Serializer::serialize($list));
        }
        return $response;
    }

    /**
     * @param int $id
     * @return Response
     *
     * @Get("/api/document/{id}")
     */
    public function getApiDocumentAction (int $id) : Response
    {
        $authService = new AuthentificationService($this->getDoctrine()->getRepository(AccessToken::class));
        $user = $authService->authenticate();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            /** @var DocumentService $documentService */
            $documentService = $this->get("AppBundle\Service\DocumentService");
            $document = $documentService->getDocument($user, $id);
            $response = new Response(json_encode(["error" => "not found"]));
            if ($document instanceof Document) {
                $response = new Response(Serializer::serialize($document));
            }
        }
        return $response;
    }

}