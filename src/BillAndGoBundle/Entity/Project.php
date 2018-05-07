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
     * @var \BillAndGoBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="BillAndGoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refUser", referencedColumnName="id")
     * })
     */
    private $refUser;

    /**
     * @var \BillAndGoBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="BillAndGoBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refClient", referencedColumnName="id")
     * })
     */
    private $refClient;



    /**
     * @var \BillAndGoBundle\Entity\Line
     *
     * @ORM\ManyToMany(targetEntity="BillAndGoBundle\Entity\Line", inversedBy="refProject")
     * @ORM\JoinTable(name="line_project",
     *		joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refLines;


    /**
     * Set begin
     *
     * @param \DateTime $begin
     *
     * @return Project
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * Get begin
     *
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return Project
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set refUser
     *
     * @param \BillAndGoBundle\Entity\User $refUser
     *
     * @return Project
     */
    public function setRefUser(\BillAndGoBundle\Entity\User $refUser = null)
    {
        $this->refUser = $refUser;

        return $this;
    }

    /**
     * Get refUser
     *
     * @return \BillAndGoBundle\Entity\User
     */
    public function getRefUser()
    {
        return $this->refUser;
    }

    /**
     * Set refClient
     *
     * @param \BillAndGoBundle\Entity\Client $refClient
     *
     * @return Project
     */
    public function setRefClient(\BillAndGoBundle\Entity\Client $refClient = null)
    {
        $this->refClient = $refClient;

        return $this;
    }

    /**
     * Get refClient
     *
     * @return \BillAndGoBundle\Entity\Client
     */
    public function getRefClient()
    {
        return $this->refClient;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add refDocument
     *
     * @param \BillAndGoBundle\Entity\Document $refDocument
     *
     * @return Project
     */
    public function addRefDocument(\BillAndGoBundle\Entity\Document $refDocument)
    {
        $this->refDocument[] = $refDocument;

        return $this;
    }

    /**
     * Remove refDocument
     *
     * @param \BillAndGoBundle\Entity\Document $refDocument
     */
    public function removeRefDocument(\BillAndGoBundle\Entity\Document $refDocument)
    {
        $this->refDocument->removeElement($refDocument);
    }

    /**
     * Get refDocument
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRefDocument()
    {
        return $this->refDocument;
    }

    /**
     * Add refLine
     *
     * @param \BillAndGoBundle\Entity\Line $refLine
     *
     * @return Project
     */
    public function addRefLine(\BillAndGoBundle\Entity\Line $refLine)
    {
        $this->refLines[] = $refLine;

        return $this;
    }

    /**
     * Remove refLine
     *
     * @param \BillAndGoBundle\Entity\Line $refLine
     */
    public function removeRefLine(\BillAndGoBundle\Entity\Line $refLine)
    {
        $this->refLines->removeElement($refLine);
    }

    /**
     * Get refLines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRefLines()
    {
        return $this->refLines;
    }
}
