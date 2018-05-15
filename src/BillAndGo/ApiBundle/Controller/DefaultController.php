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


/**
 * THE REST ROUTING IS PARTICULAR
 * WE MUST EXTENDS THE FOSRESTCONTROLLER --> IT WILL CREATE A "REST APP"
 * THE ROUTING IS BY FUNCTION NAME
 * EXAMPLE : IF YOU WANT TO DO A "GET" ON "USERS" (SO [GET] /USERS)
 * YOUR FUNCTION NAME MUST BEGIN BY "GET" THEN YOUR ROUTE NAME (SO "USERS") AND END BY "ACTION"
 */


namespace BillAndGo\ApiBundle\Controller;

use BillAndGo\ApiBundle\Entity\AccessToken;
use BillAndGo\ApiBundle\Service\AuthentificationService;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


class DefaultController extends FOSRestController
{

    /**
     * @method getApiAction
     * get --> [GET] method
     * api --> [route] = /test
     * Route --> /api
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTestAction(Request $req)
    {
        /*$headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $req->headers->set('Authorization', $headers['Authorization']);
        }*/

        $data = array("connection" => "successful");
        /** @var AuthorizationChecker $checker */
        $checker = $this->get('security.authorization_checker');
        if (!$checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $data = array("not" => "logged in");
        }
        $view = $this->view($data);
        return $this->handleView($view);
    }

    /**
     * @method getApiAction
     * get --> [GET] method
     * api --> [route] = /test2
     * Route --> /api
     *
     * @param Request $request
     * @return Response
     */
    public function getTest2Action (Request $request)
    {
        $data = array("not" => "logged in");
        $authService = new AuthentificationService($this->getDoctrine()->getRepository(AccessToken::class));
        $user = $authService->authenticate();
        if (null !== $user) {
            $data = array("user" => $user->getUsername());
        }
        $view = $this->view($data);
        return $this->handleView($view);
    }
}
