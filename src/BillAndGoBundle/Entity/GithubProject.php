<?php

namespace BillAndGoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="github_project")
 * @ORM\Entity(repositoryClass="BillAndGoBundle\Repository\GithubProjectRepository")
 */
class GithubProject
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="repository", type="string", length=255)
     */
    private $repository;

    /**
     * @var string
     *
     * @ORM\Column(name="github_id", type="string", length=255, nullable=true)
     */
    private $githubId;

    /**
     * @var string
     *
     * @ORM\Column(name="planned_column", type="string", length=255, nullable=true)
     */
    private $plannedColumn;

    /**
     * @var string
     *
     * @ORM\Column(name="working_column", type="string", length=255, nullable=true)
     */
    private $workingColumn;

    /**
     * @var string
     *
     * @ORM\Column(name="waiting_column", type="string", length=255, nullable=true)
     */
    private $waitingColumn;

    /**
     * @var string
     *
     * @ORM\Column(name="validated_column", type="string", length=255, nullable=true)
     */
    private $validatedColumn;

    /**
     * GithubProject constructor.
     *
     * @param string    $user
     * @param string    $repository
     * @param string    $githubId
     */
    public function __construct(
        string  $user,
        string  $repository,
        string  $githubId = null
    ) {
        $this->setUser($user);
        $this->setRepository($repository);
        $this->setGithubId($githubId);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return GithubProject
     */
    private function setUser(string $user): GithubProject
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     * @return GithubProject
     */
    private function setRepository(string $repository): GithubProject
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getUser()."/".$this->getRepository();
    }

    /**
     * @return string
     */
    public function getGithubId(): string
    {
        return $this->githubId;
    }

    /**
     * @param string|null $githubId
     * @return GithubProject
     */
    private function setGithubId(?string $githubId): GithubProject
    {
        $this->githubId = $githubId;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlannedColumn(): string
    {
        return $this->plannedColumn;
    }

    /**
     * @param string $plannedColumn
     * @return GithubProject
     */
    public function setPlannedColumn(string $plannedColumn): GithubProject
    {
        $this->plannedColumn = $plannedColumn;
        return $this;
    }

    /**
     * @return string
     */
    public function getWorkingColumn(): string
    {
        return $this->workingColumn;
    }

    /**
     * @param string $workingColumn
     * @return GithubProject
     */
    public function setWorkingColumn(string $workingColumn): GithubProject
    {
        $this->workingColumn = $workingColumn;
        return $this;
    }

    /**
     * @return string
     */
    public function getWaitingColumn(): string
    {
        return $this->waitingColumn;
    }

    /**
     * @param string $waitingColumn
     * @return GithubProject
     */
    public function setWaitingColumn(string $waitingColumn): GithubProject
    {
        $this->waitingColumn = $waitingColumn;
        return $this;
    }

    /**
     * @return string
     */
    public function getValidatedColumn(): string
    {
        return $this->validatedColumn;
    }

    /**
     * @param string $validatedColumn
     * @return GithubProject
     */
    public function setValidatedColumn(string $validatedColumn): GithubProject
    {
        $this->validatedColumn = $validatedColumn;
        return $this;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return [
            "planned"   => $this->getPlannedColumn(),
            "working"   => $this->getWorkingColumn(),
            "waiting"   => $this->getWaitingColumn(),
            "validated" => $this->getValidatedColumn()
        ];
    }
}