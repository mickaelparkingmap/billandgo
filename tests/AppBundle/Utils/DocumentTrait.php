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

use BillAndGoBundle\Entity\Document;
use BillAndGoBundle\Entity\User;

trait DocumentTrait
{
    /**
     * @param User $user
     * @return Document
     */
    private function createBill(User $user, array $data = []) : Document
    {
        $bill = new Document();
        $bill->setNumber('FAC-2018-01-01-001');
        if (isset($data['number'])) {
            $bill->setNumber($data['number']);
        }
        $bill->setDescription('facture de test');
        $bill->setRefUser($user);
        $bill->setStatus('draw');
        $bill->setType(false);
        return $bill;
    }

    /**
     * @param User $user
     * @return Document
     */
    private function createDraw(User $user, array $data = []) : Document
    {
        $bill = new Document();
        $bill->setNumber('DEV-2018-01-01-001');
        if (isset($data['number'])) {
            $bill->setNumber($data['number']);
        }
        $bill->setDescription('devis de test');
        $bill->setRefUser($user);
        $bill->setStatus('draw');
        $bill->setType(true);
        return $bill;
    }
}