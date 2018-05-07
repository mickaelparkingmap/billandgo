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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;


class DefaultController extends FOSRestController
{

    /**
     * @method getApiAction
     * get --> [GET] method
     * api --> [route] = /api
     * Route --> /api
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getApiAction()
    {
        $data = array("hello" => "world");
        $view = $this->view($data);
        return $this->handleView($view);
    }
}
