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


use BillAndGoBundle\Entity\Project;
use BillAndGoBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectService extends Controller
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
     * @return array
     */
    public function getProjectList (User $user) : array
    {
        return $this->entityManager
            ->getRepository(Project::class)
            ->findBy([
                "refUser" => $user
            ]);
    }

    /**
     * @param User $user
     * @param int $id
     * @return Project|null
     */
    public function getProject (User $user, int $projectID) : ?Project
    {
        $project = $this->entityManager->getRepository(Project::class)->find($projectID);
        if ($project instanceof Project) {
            if ($project->getRefUser() !== $user) {
                $project = null;
            }
        }
        return $project;
    }

}