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

class  GithubClientService extends Controller
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * GithubService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct (
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Project $project
     * @param User $user
     * @param string $repoName
     * @param bool $public
     * @return Project|null
     * @throws \Exception
     */
    public function createRepo (
        Project $project,
        User    $user,
        string  $repoName,
        bool    $public
    ): Project
    {
        if ($project->getRepoName()) {
            throw new \Exception("project already has a github repo");
        }
        if (!$user ->getGithubAccessToken() || $user->getGithubId()) {
            throw new \Exception("user does not have github access registered");
        }
        $githubClient = new GithubClient();
        $githubClient = new GithubClient();
        $githubClient->authenticate(null, $user->getGithubAccessToken(), GithubClient::AUTH_HTTP_PASSWORD);
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

        return $project;
    }
}
