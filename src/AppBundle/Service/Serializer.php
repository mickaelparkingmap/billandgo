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
        return (is_array($truc)) ? self::serializeArray($truc) : $truc->stringify;
    }

    static private function serializeArray ($array) : string
    {
        $return = [];
        foreach ($array as $elt) {
            $return[] = json_decode($elt->Serialize());
        }
        return json_encode($return);
    }
}