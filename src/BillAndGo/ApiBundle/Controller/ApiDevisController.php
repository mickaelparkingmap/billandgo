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
use BillAndGo\ApiBundle\Entity\AccessToken;
use BillAndGo\ApiBundle\Service\AuthentificationService;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

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
        $list = ["error" => "not connected"];

        if (null !== $user) {
            /** @var DevisService $devisService */
            $devisService = $this->get("AppBundle\Service\DevisService");
            $list = $devisService->listDrawFromUser($user);
        }
        $view = $this->view($list);
        return $this->handleView($view);
    }

}