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

use BillAndGoBundle\Entity\Line;
use BillAndGoBundle\Entity\Project;
use BillAndGoBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Github\Api\Repo;
use Github\Api\Repository\Projects;
use Github\Exception\MissingArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Github\Client as GithubClient;

/**
 * Class GithubClientService
 * @package AppBundle\Service
 */
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
     * @param User $user
     * @return GithubClient
     * @throws \Exception
     */
    private function getAuthenticatedClient(User $user): GithubClient
    {
        if (!$user->getGithubAccessToken() || !$user->getGithubId()) {
            throw new \Exception("user does not have github access registered");
        }
        $githubClient = new GithubClient();
        $githubClient->authenticate(null, $user->getGithubAccessToken(), GithubClient::AUTH_HTTP_PASSWORD);

        return $githubClient;
    }

    /**
     * @param User      $user
     * @param Project   $project
     * @throws MissingArgumentException
     * @throws \Exception
     */
    private function setProject (
        User    $user,
        Project $project
    ) {
        $explodedRepo = explode("/", $project->getRepoName());
        $userName = $explodedRepo[0];
        $repoName = $explodedRepo[1];

        $githubClient = $this->getAuthenticatedClient($user);
        /** @var Repo $githubRepoApi */
        $githubRepoApi = $githubClient->api("repo");
        /** @var Projects $githubProjectApi */
        $githubProjectApi = $githubRepoApi->projects()->configure();
        $gitProject = $githubProjectApi->create(
            $userName,
            $repoName,
            array('name' => $repoName)
        );

        /** set columns */
        $githubColumnApi = $githubProjectApi->columns()->configure();
        $columnTodo = $githubColumnApi->create($gitProject["id"], ['name' => 'todo']);
        $githubColumnApi->create($gitProject["id"], ['name' => 'doing']);
        $githubColumnApi->create($gitProject["id"], ['name' => 'test']);
        $githubColumnApi->create($gitProject["id"], ['name' => 'done']);

        /** create cards */
        $githubCardsApi = $githubColumnApi->cards()->configure();
        /** @var Line $line */
        foreach ($project->getRefLines() as $line) {
            $card = $githubCardsApi->create($columnTodo["id"], ['note' =>$line->getDescription()]);
            $line->setGithubCard($card["id"]);
            $this->entityManager->persist($line);
        }
        $this->entityManager->flush();
    }

    /**
     * @param Project   $project
     * @param User      $user
     * @param string    $repoName
     * @param bool      $public
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
        $githubClient = $this->getAuthenticatedClient($user);
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
        try {
            $this->setProject($user, $project);
        } catch (\Exception $e) {
            throw new \Exception("GitHub : ".$e->getMessage());
        }

        return $project;
    }

    /**
     * @param User $user
     * @return ArrayCollection
     * @throws \Exception
     */
    public function listOfRepo (
        User $user
    ) : ArrayCollection
    {
        $githubClient = $this->getAuthenticatedClient($user);
        $repoArray = $githubClient->currentUser()->repositories();

        return new ArrayCollection($repoArray);
    }
}
