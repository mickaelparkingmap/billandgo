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
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class ApiProfileController extends ApiBasicController
{
    /**
     * @return Response
     *
     * @Get("/api/profile")
     */
    public function getProfileAction () : Response
    {
        $user = $this->getUser();
        $response = new Response(json_encode(["error" => "not connected"]));

        if (null !== $user) {
            $response = new Response(Serializer::serialize($user));
        }
        return $response;
    }


}