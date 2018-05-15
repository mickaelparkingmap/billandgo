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

use BillAndGoBundle\Entity\Devis;
use BillAndGoBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DevisService extends Controller
{
    /** @var EntityManager */
    private $em;

    public function __construct (
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    public function listDrawFromUser (User $user) : array
    {
        return $this->em
            ->getRepository(Devis::class)
            ->findBy([
                'refUser' => $user
            ])
            ;
    }
}