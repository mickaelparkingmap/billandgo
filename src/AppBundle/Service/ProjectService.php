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
use Doctrine\ORM\EntityNotFoundException;
use Github\Api\Repo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Github\Client as GithubClient;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @param int $projectID
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

    /**
     * @param User $user
     * @param int $projectID
     * @param string $repoName
     * @param bool $public
     * @return Project|null
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function createRepo (
        User    $user,
        int     $projectID,
        string  $repoName,
        bool    $public
    ): ?Project
    {
        /** @var Project|null $project */
        $project = $this->entityManager->getRepository(Project::class)->find($projectID);
        if (!$project) {
            throw new EntityNotFoundException();
        }
        if ($project->getRefUser() !== $user) {
            throw new AccessDeniedException();
        }
        if (!$project->getRepoName() && $user->getGithubAccessToken() && $user->getGithubId()) {
            $githubClient = new GithubClient();
            //@todo replace by using user.auth
            $githubClient->authenticate("*", "*", GithubClient::AUTH_HTTP_PASSWORD);
            /** @var Repo $githubRepoApi */
            $githubRepoApi = $githubClient->api("repo");
            try {
                $apiResponse = $githubRepoApi->create($repoName, $project->getDescription(), null, $public);
                $repoFullName = $apiResponse["full_name"];
                $project->setRepoName($repoFullName);
                $this->entityManager->persist($project);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                throw new \Exception("GitHub : ".$e->getMessage());
            }
        }

        return $project;
    }

}