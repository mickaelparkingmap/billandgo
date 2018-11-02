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

use BillAndGoBundle\Entity\Suggestion;
use BillAndGoBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SuggestionService
 * @package BillAndGoBundle\Service
 */
class SuggestionService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * SuggestionService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @param string $name
     * @return Suggestion
     */
    public function create(
        User    $user,
        string  $name
    ): Suggestion
    {
        $suggestion = new Suggestion();
        $suggestion
            ->setRefUser($user)
            ->setName($name)
        ;
        $this->entityManager->persist($suggestion);
        return $suggestion;
    }

    /**
     * @param User $user
     * @param string $name
     * @return Suggestion|null
     */
    public function getOne(User $user, string $name): ?Suggestion
    {
        $suggestionRepo = $this->entityManager->getRepository(Suggestion::class);
        /** @var Suggestion|null $suggestion */
        $suggestion = $suggestionRepo->findOneBy([
            'refUser'   => $user,
            'name'      => $name
        ]);
        return $suggestion;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getList(User $user): array
    {
        $suggestionRepo = $this->entityManager->getRepository(Suggestion::class);
        /** @var Suggestion|null $suggestion */
        $suggestionListUser = $suggestionRepo->findBy([
            'refUser'   => $user
        ]);
        $suggestionListCommon = $suggestionRepo->findBy([
            'refUser'   => 1
        ]);
        $suggestionList = [];
        foreach ($suggestionListUser as $suggestion) {
            $suggestionList[$suggestion->getName()] = $suggestion;
        }
        foreach ($suggestionListCommon as $suggestion) {
            if (!isset($suggestionList[$suggestion->getName()])) {
                $suggestionList[$suggestion->getName()] = $suggestion;
            }
        }
        return $suggestionList;
    }

    /**
     * @param User $user
     * @param string $name
     * @param null|string $description
     * @param float|null $priceHT
     * @param float|null $time
     * @return Suggestion
     */
    public function update(
        User    $user,
        string  $name,
        ?string  $description = null,
        ?float   $priceHT = null,
        ?float   $time = null
    ): Suggestion
    {
        $suggestion = $this->getOne($user, $name);
        if (null === $suggestion) {
            $suggestion = $this->create($user, $name);
        }
        $suggestion
            ->setDescription($description)
            ->setPriceHT($priceHT)
            ->setTime($time)
        ;
        return $suggestion;
    }

}