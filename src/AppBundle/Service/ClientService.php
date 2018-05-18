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
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * DevisService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct (
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getClientListFromUser (User $user) : array
    {
        return $this->entityManager
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
    public function getClient (User $user, int $clientID) : ?Client
    {
        $client = $this->entityManager->getRepository(Client::class)->find($clientID);
        if ($client instanceof Client) {
            if ($client->getUserRef() !== $user) {
                $client = null;
            }
        }
        return $client;
    }

}