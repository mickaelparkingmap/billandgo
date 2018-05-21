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
    private $entityManager;

    /**
     * DevisService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct (
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @param null|string $begin
     * @param null|string $end
     * @param array $status
     * @return Document[]
     */
    public function listDrawFromUser (
        User $user,
        ?string $begin = null,
        ?string $end = null,
        array $status = [
            "draw", "canceled", "refused",
            "estimated", "accepted"
        ]
    ) : array
    {
        return $this
            ->entityManager
            ->getRepository(Document::class)
            ->findByDate($user, 'estimate', $begin, $end, $status);
    }

    /**
     * @param User $user
     * @param null|string $begin
     * @param null|string $end
     * @param array $status
     * @return Document[]
     */
    public function listBillsFromUser (
        User $user,
        ?string $begin = null,
        ?string $end = null,
        array $status = [
            "draw", "canceled", "refused",
            "billed", "partially", "paid"
        ]
    ) : array
    {
        return $this
            ->entityManager
            ->getRepository(Document::class)
            ->findByDate($user, 'bill', $begin, $end, $status);
    }

    /**
     * @param User $user
     * @param int $docID
     * @return Document
     */
    public function getDocument (
        User $user,
        int $docID
    ) : ?Document
    {
        $document = $this->entityManager->getRepository(Document::class)->find($docID);
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
     * @param Client $client
     * @return Document
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function documentCreation (User $user, string $type, Client $client) : Document
    {
        $index = $this->numerotation($user, $type);
        $number = ('bill' === $type) ? 'FAC-' : 'DEV-';
        $number .= date('Y-m-') . str_pad($index, 3, '0', STR_PAD_LEFT);

        $document = new Document();
        $document->setRefUser($user);
        $document->setNumber($number);
        $document->setType(('estimate' === $type));
        $document->setStatus('draw');
        $document->setRefClient($client);
        $this->entityManager->persist($document);
        $this->entityManager->flush();

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
        $numerotationArray = $this->entityManager->getRepository(Numerotation::class)->findBy([
            'refUser' => $user
        ]);
        if (isset($numerotationArray[0])) {
            /** @var Numerotation $num */
            $num = $numerotationArray[0];
            $index = $this->updateNumerotation($type, $num);
        }
        else {
            $index = $this->createNumerotation($user, $type);
        }
        return $index;
    }

    /**
     * @param User $user
     * @param string $type
     * @return integer
     */
    private function createNumerotation (User $user, string $type) : int
    {
        $num = new Numerotation();
        $num->setRefUser($user);
        $num->setBillIndex(("bill" === $type) ? 1 : 0);
        $num->setEstimateIndex(("estimate" === $type) ? 1 : 0);
        $num->setBillYearMonth(date("Ym"));
        $num->setEstimateYearMonth(date("Ym"));
        $this->entityManager->persist($num);
        $index = ('estimate' === $type) ? $num->getEstimateIndex() : $num->getBillIndex();
        return $index;
    }

    /**
     * @param string $type
     * @param Numerotation $num
     * @return integer
     */
    private function updateNumerotation (string $type, Numerotation $num) : int
    {
        if ('estimate' === $type) {
            if ($num->getEstimateYearMonth() != date("Ym")) {
                $num->setEstimateYearMonth(date("Ym"));
                $num->setEstimateIndex(1);
            }
            else {
                $num->setEstimateIndex($num->getEstimateIndex() + 1);
            }
            $this->entityManager->persist($num);
            $index = $num->getEstimateIndex();
        }
        else if ('bill' === $type) {
            if ($num->getBillYearMonth() != date("Ym")) {
                $num->setBillYearMonth(date("Ym"));
                $num->setBillIndex(1);
            }
            else {
                $num->setBillIndex($num->getBillIndex() + 1);
            }
            $this->entityManager->persist($num);
            $index = $num->getBillIndex();
        }
        return $index;
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
            $this->entityManager->flush();
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
            $this->entityManager->flush();
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
            $this->entityManager->flush();
        }
        return $doc;
    }
}