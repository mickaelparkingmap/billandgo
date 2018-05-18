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
use BillAndGoBundle\Entity\Document;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiDevisController
 * @package BillAndGo\ApiBundle\Controller
 */
class ApiDocumentController extends ApiBasicController
{
    /** @var DocumentService */
    private $documentService;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->documentService = $this->get("AppBundle\Service\DocumentService");
    }


    /**
     * @return Response
     *
     * @Get("/api/estimate")
     */
    public function getApiEstimateIndexAction () : Response
    {
        $user = $this->getUser();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $list = $this->documentService->listDrawFromUser($user);
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
        $user = $this->getUser();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $list = $this->documentService->listBillsFromUser($user);
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
    public function getApiDocumentAction (int $docID) : Response
    {
        $user = $this->getUser();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $document = $this->documentService->getDocument($user, $docID);
            $response = new Response(json_encode(["error" => "not found"]));
            if ($document instanceof Document) {
                $response = new Response(Serializer::serialize($document));
            }
        }
        return $response;
    }

}