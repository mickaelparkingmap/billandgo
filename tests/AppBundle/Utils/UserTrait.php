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

use BillAndGoBundle\Entity\User;

trait UserTrait
{
    /**
     * @return User
     */
    private function createUser() : User
    {
        $user = new User();
        $user->setFirstname("jean-mi");
        $user->setLastname("dupont");
        $user->setUsername("jean-mi_dupont");
        $user->setCompanyname("dupont");
        $user->setEmail("jmdupont@gmail.com");
        $user->setPassword("toto");
        return $user;
    }
}