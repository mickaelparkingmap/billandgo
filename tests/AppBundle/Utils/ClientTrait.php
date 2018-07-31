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

namespace Tests\AppBundle\Utils;

use BillAndGoBundle\Entity\Client;
use BillAndGoBundle\Entity\User;

trait ClientTrait
{
    /**
     * @param User $user
     * @param array $data
     * @return Client
     */
    private function createClient (User $user, array $data = []) : Client
    {
        $client = new Client();
        $client->setCompanyName('google');
        if (isset($data['companyName'])) {
            $client->setCompanyName($data['companyName']);
        }
        $client->setUserRef($user);
        $client->setAdress('partout');
        $client->setCity('everywhere');
        $client->setZipcode('00000');
        $client->setCountry('World');
        return $client;
    }
}