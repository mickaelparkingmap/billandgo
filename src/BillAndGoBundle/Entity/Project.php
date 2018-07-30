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


namespace BillAndGoBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity
 */
class Project
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="begin", type="datetime", nullable=false)
     */
    private $begin = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refUser", referencedColumnName="id")
     * })
     */
    private $refUser;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refClient", referencedColumnName="id")
     * })
     */
    private $refClient;



    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Line", inversedBy="refProject")
     * @ORM\JoinTable(name="line_project",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *      )
     */
    private $refLines;


    /**
     * Set begin
     *
     * @param \DateTime|null $begin
     *
     * @return Project
     */
    public function setBegin(?\DateTime $begin) : self
    {
        $this->begin = $begin;
        return $this;
    }



    /**
     * Get begin
     *
     * @return \DateTime|null
     */
    public function getBegin() : ?\DateTime
    {
        return $this->begin;
    }

    /**
     * Set deadline
     *
     * @param \DateTime|null $deadline
     *
     * @return Project
     */
    public function setDeadline(?\DateTime $deadline) : self
    {
        $this->deadline = $deadline;
        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime|null
     */
    public function getDeadline() : ?\DateTime
    {
        return $this->deadline;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Project
     */
    public function setName(string $name) : self
    {
        $this->name = trim(strip_tags($name));
        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description|null
     *
     * @return Project
     */
    public function setDescription(?string $description) : self
    {
        $this->description = trim(strip_tags($description));
        return $this;
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set refUser
     *
     * @param User|null $refUser
     *
     * @return Project
     */
    public function setRefUser(?User $refUser = null) : self
    {
        $this->refUser = $refUser;
        return $this;
    }

    /**
     * Get refUser
     *
     * @return User|null
     */
    public function getRefUser() : ?User
    {
        return $this->refUser;
    }

    /**
     * Set refClient
     *
     * @param Client $refClient
     *
     * @return Project
     */
    public function setRefClient(Client $refClient = null) : self
    {
        $this->refClient = $refClient;
        return $this;
    }

    /**
     * Get refClient
     *
     * @return Client|null
     */
    public function getRefClient() : ?Client
    {
        return $this->refClient;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->refLines = new  ArrayCollection();
        $this->lines = new ArrayCollection();
    }

    /**
     * Add refLine
     *
     * @param Line $refLine
     *
     * @return Project
     */
    public function addRefLine(Line $refLine) : self
    {
        $this->refLines->add($refLine);
        return $this;
    }

    /**
     * Remove refLine
     *
     * @param Line $refLine
     *
     * @return Project
     */
    public function removeRefLine(Line $refLine) : self
    {
        $this->refLines->removeElement($refLine);
        return $this;
    }

    /**
     * Get refLines
     *
     * @return Collection
     */
    public function getRefLines() : Collection
    {
        return $this->refLines;
    }

    /**
     * @return string
     */
    public function stringify() : string
    {
        $data = [
            'id'            => $this->id,
            'name'          => $this->name,
            'client'        => $this->refClient->getCompanyName(),
            'clientID'      => $this->refClient->getId(),
            'description'   => $this->description,
            'begin'         => $this->begin->format('y-m-d H:i:s'),
            'deadline'      => $this->deadline->format('y-m-d H:i:s')
        ];
        $data['todo'] = [];
        foreach ($this->refLines as $line) {
            /** @var Line $line */
            $data['todo'][$line->getId()] = [
                'id'            => $line->getId(),
                'name'          => $line->getName(),
                'description'   => $line->getDescription(),
                'valueHt'       => $line->getQuantity() * $line->getPrice(),
                'status'        => $line->getStatus()
            ];
        }
        return json_encode($data);
    }
}
