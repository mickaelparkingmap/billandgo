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
    private function createUser($data = []) : User
    {
        $user = new User();
        $user->setFirstname("jean-mi");
        if (isset($data['firstname'])) {
            $user->setFirstname($data['firstname']);
        }
        $user->setLastname("dupont");
        $user->setUsername($user->getFirstname().'_'.$user->getLastname());
        $user->setCompanyname("dupont");
        $user->setEmail("jmdupont@gmail.com");
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        $user->setPassword("toto");
        return $user;
    }
}