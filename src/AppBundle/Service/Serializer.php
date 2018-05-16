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

namespace AppBundle\Service;

/**
 * Class Serializer
 * @package AppBundle\Service
 */
class Serializer
{
    /**
     * @param $truc
     * @return string
     */
    static public function serialize ($truc) : string
    {
        if (is_array($truc)) {
            $array = [];
            foreach ($truc as $elt) {
                $array[] = json_decode($elt->__toString());
            }
            return json_encode($array);
        }
        else {
            return $truc->Serialize();
        }
    }
}