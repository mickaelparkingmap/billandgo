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

use BillAndGoBundle\Entity\Document;
use BillAndGoBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DevisService
 * @package AppBundle\Service
 */
class DocumentService extends Controller
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
     * @param array $status
     * @return Document[]
     */
    public function listDrawFromUser (
        User $user,
        array $status = [
            "draw", "canceled", "refused",
            "estimated", "accepted"
        ]
    ) : array
    {
        $filter = [
            'refUser'   => $user,
            'type'      => true,
            'status'    => $status
        ];
        return $this->em
            ->getRepository(Document::class)
            ->findBy($filter);
    }

    /**
     * @param User $user
     * @param array $status
     * @return Document[]
     */
    public function listBillsFromUser (
        User $user,
        array $status = [
            "draw", "canceled", "refused",
            "billed", "partially", "paid"
        ]
    ) : array
    {
        $filter = [
            'refUser'   => $user,
            'type'      => false,
            'status'    => $status
        ];
        return $this->em
            ->getRepository(Document::class)
            ->findBy($filter);
    }

    /**
     * @param User $user
     * @param int $id
     * @return Document
     */
    public function getDocument (
        User $user,
        int $id
    ) : ?Document
    {
        $document = $this->em->getRepository(Document::class)->find($id);
        if ($document instanceof Document) {
            if ($document->getRefUser() !== $user) {
                $document = null;
            }
        }
        return $document;
    }
}