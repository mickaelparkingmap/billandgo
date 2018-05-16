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
    public function getProjectList (User $user) : array
    {
        return $this->em
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
    public function getProject (User $user, int $id) : ?Project
    {
        $project = $this->em->getRepository(Project::class)->find($id);
        if ($project instanceof Project) {
            if ($project->getRefUser() !== $user) {
                $project = null;
            }
        }
        return $project;
    }

}