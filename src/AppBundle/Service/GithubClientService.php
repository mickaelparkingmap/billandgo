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

use BillAndGoBundle\Entity\GithubProject;
use BillAndGoBundle\Entity\Line;
use BillAndGoBundle\Entity\Project;
use BillAndGoBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Github\Api\Project\Cards;
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
     * @param Line $line
     * @param Project $project
     * @param string $newStatus
     * @return bool
     * @throws \Exception
     */
    public function moveCard(Line $line, Project $project, string $newStatus): bool
    {
        $githubClient = $this->getAuthenticatedClient($project->getRefUser());
        /** @var Repo $githubRepoApi */
        $githubRepoApi = $githubClient->api("repo");
        /** @var Cards $githubCardApi */
        $githubCardApi = $githubRepoApi->projects()->columns()->cards()->configure();

        $columns = $project->getGithubProject()->getColumns();
        if (!$columns[$newStatus]) {
            return false;
        }
        $columnId = intval($columns[$newStatus]);
        try {
            $githubCardApi->move($line->getGithubCard(), ["position" => "bottom", "column_id" => $columnId]);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @param Projects  $githubProjectApi
     * @param Project   $project
     * @return GithubProject
     */
    private function createCards(Projects $githubProjectApi, Project $project): GithubProject
    {
        $githubCardsApi = $githubProjectApi->columns()->cards()->configure();

        foreach ($project->getRefLines() as $line) {
            $card = $githubCardsApi->create(
                $project->getGithubProject()->getPlannedColumn(),
                ['note' => $line->getDescription()]
            );
            $line->setGithubCard($card["id"]);
            $this->entityManager->persist($line);
        }

        return $project->getGithubProject();
    }

    /**
     * @param Projects      $githubProjectApi
     * @param GithubProject $project
     * @return GithubProject
     * @throws MissingArgumentException
     */
    private function createColumns(Projects $githubProjectApi, GithubProject $project): GithubProject
    {
        $githubColumnApi = $githubProjectApi->columns()->configure();
        $columnPlanned = $githubColumnApi->create($project->getGithubId(), ['name' => 'todo']);
        $project->setPlannedColumn($columnPlanned["id"]);
        $columnWorking = $githubColumnApi->create($project->getGithubId(), ['name' => 'doing']);
        $project->setWorkingColumn($columnWorking["id"]);
        $columnWaiting = $githubColumnApi->create($project->getGithubId(), ['name' => 'test']);
        $project->setWaitingColumn($columnWaiting["id"]);
        $columnValidated = $githubColumnApi->create($project->getGithubId(), ['name' => 'done']);
        $project->setValidatedColumn($columnValidated["id"]);

        return $project;
    }

    /**
     * @param Project $project
     * @param string $repoFullName
     * @return GithubProject
     * @throws MissingArgumentException
     * @throws \Exception
     */
    private function createGithubProject (
        Project $project,
        string  $repoFullName
    ): GithubProject {
        $explodedRepo = explode("/", $repoFullName);
        $userName = $explodedRepo[0];
        $repoName = $explodedRepo[1];

        $githubClient = $this->getAuthenticatedClient($project->getRefUser());
        /** @var Repo $githubRepoApi */
        $githubRepoApi = $githubClient->api("repo");
        /** @var Projects $githubProjectApi */
        $githubProjectApi = $githubRepoApi->projects()->configure();
        $gitProject = $githubProjectApi->create(
            $userName,
            $repoName,
            array('name' => $repoName)
        );
        $githubProject = new GithubProject($userName, $repoName, $gitProject["id"]);
        $project->setGithubProject($githubProject);
        $this->entityManager->persist($project);

        $this->createColumns($githubProjectApi, $githubProject);
        $githubProject = $this->createCards($githubProjectApi, $project);
        $this->entityManager->persist($githubProject);

        $this->entityManager->flush();

        return $githubProject;
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
        if ($project->getGithubProject()) {
            throw new \Exception("project already has a github repo");
        }
        $githubClient = $this->getAuthenticatedClient($user);
        /** @var Repo $githubRepoApi */
        $githubRepoApi = $githubClient->api("repo");
        try {
            $apiResponse = $githubRepoApi->create($repoName, $project->getDescription(), null, $public);
            $repoFullName = $apiResponse["full_name"];
        } catch (\Exception $e) {
            throw new \Exception("GitHub : ".$e->getMessage());
        }
        try {
            $this->createGithubProject($project, $repoFullName);
        /*try {
            $this->setProject($user, $project);*/
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
