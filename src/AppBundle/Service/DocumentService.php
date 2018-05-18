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
use BillAndGoBundle\Entity\Document;
use BillAndGoBundle\Entity\Numerotation;
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

    /**
     * @param User $user
     * @param string $type
     * @return Document
     * @throws \Doctrine\ORM\ORMException
     */
    public function documentCreation (User $user, string $type) : Document
    {
        $index = $this->numerotation($user, $type);
        $number = ('bill' === $type) ? 'FAC-' : 'DEV-';
        $number .= date('Y-m-') . str_pad($index, 3, '0', STR_PAD_LEFT);

        $document = new Document();
        $document->setRefUser($user);
        $document->setNumber($number);
        $document->setType(('estimate' === $type));
        $document->setStatus('draw');
        $this->em->persist($document);
        $this->em->flush();

        return $document;
    }

    /**
     * @param User $user
     * @param string $type
     * @return int
     * @throws \Doctrine\ORM\ORMException
     */
    private function numerotation (User $user, string $type) : int
    {
        $numerotationArray = $this->em->getRepository(Numerotation::class)->findBy([
            'refUser' => $user
        ]);
        if (isset($numerotationArray[0])) {
            /** @var Numerotation $num */
            $num = $numerotationArray[0];
            $this->updateNumerotation($type, $num);
        }
        else {
            $this->createNumerotation($user, $type);
        }
        return $num->getEstimateIndex();
    }

    /**
     * @param User $user
     * @param string $type
     * @return Numerotation
     */
    private function createNumerotation (User $user, string $type) : Numerotation
    {
        $num = new Numerotation();
        $num->setRefUser($user);
        $num->setBillIndex(("bill" === $type) ? 1 : 0);
        $num->setEstimateIndex(("estimate" === $type) ? 1 : 0);
        $num->setBillYearMonth(date("Ym"));
        $num->setEstimateYearMonth(date("Ym"));
        $this->em->persist($num);
        return $num;
    }

    /**
     * @param string $type
     * @param Numerotation $num
     * @return Numerotation
     */
    private function updateNumerotation (string $type, Numerotation $num) : Numerotation
    {
        if ('estimate' === $type) {
            if ($num->getEstimateYearMonth() != date("Ym")) {
                $num->setEstimateYearMonth(date("Ym"));
                $num->setEstimateIndex(1);
            }
            else {
                $num->setEstimateIndex($num->getEstimateIndex() + 1);
            }
            $this->em->persist($num);
        }
        else if ('bill' === $type) {
            if ($num->getBillYearMonth() != date("Ym")) {
                $num->setBillYearMonth(date("Ym"));
                $num->setBillIndex(1);
            }
            else {
                $num->setBillIndex($num->getBillIndex() + 1);
            }
            $this->em->persist($num);

        }
        return $num;
    }

    /**
     * @param User $user
     * @param Client $client
     * @param int $docID
     * @return Document|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClient (User $user, Client $client, int $docID) : ?Document
    {
        $doc = $this->getDocument($user, $docID);
        if ($doc instanceof Document) {
            $doc->setRefClient($client);
            $this->em->flush();
        }
        return $doc;
    }

    /**
     * @param User $user
     * @param string $description
     * @param int $docID
     * @return Document|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setDescription (User $user, string $description, int $docID) : ?Document
    {
        $doc = $this->getDocument($user, $docID);
        if ($doc instanceof Document) {
            $doc->setDescription($description);
            $this->em->flush();
        }
        return $doc;
    }

    /**
     * @param User $user
     * @param \DateTime $delay
     * @param int $docID
     * @return Document|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setDelayDate (User $user, \DateTime $delay, int $docID) : ?Document
    {
        $doc = $this->getDocument($user, $docID);
        if ($doc instanceof Document) {
            $doc->setDelayDate($delay);
            $this->em->flush();
        }
        return $doc;
    }
}