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
use Github\Api\Issue;
use Github\Api\Repo;
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
        $githubClient->authenticate("mbuliard", "inregnodatisumus", GithubClient::AUTH_HTTP_PASSWORD);
        //$githubClient->authenticate(null, $user->getGithubAccessToken(), GithubClient::AUTH_HTTP_PASSWORD);

        return $githubClient;
    }

    /**
     * @param User $user
     * @param Project $project
     * @throws \Exception
     */
    private function setIssues (
        User    $user,
        Project $project
    ) {
        $explodedRepo = explode("/", $project->getRepoName());
        $userName = $explodedRepo[0];
        $repoName = $explodedRepo[1];
        $githubClient = $this->getAuthenticatedClient($user);

        /** @var Repo $githubRepoApi */
        $githubRepoApi = $githubClient->api("repo");
        $githubRepoApi->update(
            $userName,
            $repoName,
            ['has_issues' => true, 'name' => $repoName, "description" => $project->getDescription()]
        );

        /** @var Issue $githubIssueApi */
        $githubIssueApi = $githubClient->api("issue");
        /** @var Line $line */
        foreach ($project->getRefLines() as $line) {
            $githubIssueApi->create(
                $userName,
                $repoName,
                array('title' => $line->getName(), 'body' => $line->getDescription())
            );
        }
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
            $this->setIssues($user, $project);
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

    /**
     * @param User      $user
     * @param Project   $project
     * @param string    $issueTitle
     * @param string    $issueBody
     * @return Project
     * @throws \Exception
     */
    public function createIssue(
        User    $user,
        Project $project,
        string  $issueTitle,
        string  $issueBody
    ): Project {
        if (!$project->getRepoName()) {
            throw new \Exception("project does not have github repository registered");
        }
        $explodedRepo = explode("/", $project->getRepoName());
        $userName = $explodedRepo[0];
        $repoName = $explodedRepo[1];

        try {
            $githubClient = $this->getAuthenticatedClient($user);

            /** @var Issue $githubIssueApi */
            $githubIssueApi = $githubClient->api("issue");
            $githubIssueApi->create($userName, $repoName, array('title' => $issueTitle, 'body' => $issueBody));
        } catch (MissingArgumentException $e) {
            throw new \Exception("Github : " . $e->getMessage());
        }

        return $project;
    }
}
