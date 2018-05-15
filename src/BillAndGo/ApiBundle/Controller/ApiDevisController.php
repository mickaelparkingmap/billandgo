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

use AppBundle\Service\DevisService;
use AppBundle\Service\Serializer;
use BillAndGo\ApiBundle\Entity\AccessToken;
use BillAndGo\ApiBundle\Service\AuthentificationService;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiDevisController
 * @package BillAndGo\ApiBundle\Controller
 */
class ApiDevisController extends FOSRestController
{
    /**
     * @method getApiDevisIndexAction
     * get --> [GET] method
     * api --> [route] = /api/devis/index
     * Route --> /api
     * @return Response
     */
    public function getApiDevisIndexAction () : Response
    {

        $authService = new AuthentificationService($this->getDoctrine()->getRepository(AccessToken::class));
        $user = $authService->authenticate();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            /** @var DevisService $devisService */
            $devisService = $this->get("AppBundle\Service\DevisService");
            $list = $devisService->listDrawFromUser($user);
            $response = new Response(Serializer::serialize($list));
        }
        return $response;
    }

}