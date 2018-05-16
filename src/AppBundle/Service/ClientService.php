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


use BillAndGoBundle\Entity\Client;
use BillAndGoBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientService extends Controller
{
    /** @var EntityManager */
    private $em;

    /**
     * DevisService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct (
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getClientListFromUser (User $user) : array
    {
        return $this->em
            ->getRepository(Client::class)
            ->findBy([
                "userRef" => $user
            ]);
    }

    /**
     * @param User $user
     * @param int $id
     * @return Client|null
     */
    public function getClient (User $user, int $id) : ?Client
    {
        $client = $this->em->getRepository(Client::class)->find($id);
        if ($client instanceof Client) {
            if ($client->getUserRef() !== $user) {
                $client = null;
            }
        }
        return $client;
    }

}