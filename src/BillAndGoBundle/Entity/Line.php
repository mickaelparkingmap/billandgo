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

/**
 * Line
 *
 * @ORM\Table(name="line")
 * @ORM\Entity(repositoryClass="BillAndGoBundle\Repository\LineRepository")
 */
class Line
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="smallint")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $price;

    /**
     * @var \BillAndGoBundle\Entity\Tax
     *
     * @ORM\ManyToOne(targetEntity="BillAndGoBundle\Entity\Tax")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refTax", referencedColumnName="id")
     * })
     */
    private $refTax;

    /**
     * @var string
     *
     * @ORM\Column(name="estimatedTime", type="decimal", precision=4, scale=1, nullable=true)
     */
    private $estimatedTime;

    /**
     * @var string
     *
     * @ORM\Column(name="chronoTime", type="decimal", precision=4, scale=1, nullable=true)
     */
    private $chronoTime;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadLine", type="date", nullable=true)
     */
    private $deadLine;

    /**
     * @var \BillAndGoBundle\Entity\Project
     *
     * @ORM\ManyToMany(targetEntity="BillAndGoBundle\Entity\Project", mappedBy="refLines")
     * @ORM\JoinTable(name="line_project",
     *		joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refProject;

    /**
     * @var \BillAndGoBundle\Entity\Document
     *
     * @ORM\ManyToMany(targetEntity="BillAndGoBundle\Entity\Document", mappedBy="refLines")
     * @ORM\JoinTable(name="line_estimate",
     *		joinColumns={@ORM\JoinColumn(name="estimate_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refEstimate;

    /**
     * @var \BillAndGoBundle\Entity\Document
     *
     * @ORM\ManyToMany(targetEntity="BillAndGoBundle\Entity\Document", mappedBy="refLinesB")
     * @ORM\JoinTable(name="line_bill",
     *		joinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refBill;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Line
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
     * @return Line
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Line
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Line
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set estimatedTime
     *
     * @param string $estimatedTime
     *
     * @return Line
     */
    public function setEstimatedTime($estimatedTime)
    {
        $this->estimatedTime = $estimatedTime;

        return $this;
    }

    /**
     * Get estimatedTime
     *
     * @return string
     */
    public function getEstimatedTime()
    {
        return $this->estimatedTime;
    }

    /**
     * Set chronoTime
     *
     * @param string $chronoTime
     *
     * @return Line
     */
    public function setChronoTime($chronoTime)
    {
        $this->chronoTime = $chronoTime;

        return $this;
    }

    /**
     * Get chronoTime
     *
     * @return string
     */
    public function getChronoTime()
    {
        return $this->chronoTime;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Line
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set deadLine
     *
     * @param \DateTime $deadLine
     *
     * @return Line
     */
    public function setDeadLine($deadLine)
    {
        $this->deadLine = $deadLine;

        return $this;
    }

    /**
     * Get deadLine
     *
     * @return \DateTime
     */
    public function getDeadLine()
    {
        return $this->deadLine;
    }

    /**
     * Set refUser
     *
     * @param \BillAndGoBundle\Entity\User $refUser
     *
     * @return Line
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
     * @return Line
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
        $this->refProject = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add refProject
     *
     * @param \BillAndGoBundle\Entity\Project $refProject
     *
     * @return Line
     */
    public function addRefProject(\BillAndGoBundle\Entity\Project $refProject)
    {
        $this->refProject[] = $refProject;

        return $this;
    }

    /**
     * Remove refProject
     *
     * @param \BillAndGoBundle\Entity\Project $refProject
     */
    public function removeRefProject(\BillAndGoBundle\Entity\Project $refProject)
    {
        $this->refProject->removeElement($refProject);
    }

    /**
     * Get refProject
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRefProject()
    {
        return $this->refProject;
    }

    /**
     * Add refEstimate
     *
     * @param \BillAndGoBundle\Entity\Document $refEstimate
     *
     * @return Line
     */
    public function addRefEstimate(\BillAndGoBundle\Entity\Document $refEstimate)
    {
        $this->refEstimate[] = $refEstimate;

        return $this;
    }

    /**
     * Remove refEstimate
     *
     * @param \BillAndGoBundle\Entity\Document $refEstimate
     */
    public function removeRefEstimate(\BillAndGoBundle\Entity\Document $refEstimate)
    {
        $this->refEstimate->removeElement($refEstimate);
    }

    /**
     * Get refEstimate
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRefEstimate()
    {
        return $this->refEstimate;
    }

    /**
     * Add refBill
     *
     * @param \BillAndGoBundle\Entity\Document $refBill
     *
     * @return Line
     */
    public function addRefBill(\BillAndGoBundle\Entity\Document $refBill)
    {
        $this->refBill[] = $refBill;

        return $this;
    }

    /**
     * Remove refBill
     *
     * @param \BillAndGoBundle\Entity\Document $refBill
     */
    public function removeRefBill(\BillAndGoBundle\Entity\Document $refBill)
    {
        $this->refBill->removeElement($refBill);
    }

    /**
     * Get refBill
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRefBill()
    {
        return $this->refBill;
    }

    /**
     * Set refTax
     *
     * @param \BillAndGoBundle\Entity\Tax $refTax
     *
     * @return Line
     */
    public function setRefTax(\BillAndGoBundle\Entity\Tax $refTax = null)
    {
        $this->refTax = $refTax;

        return $this;
    }

    /**
     * Get refTax
     *
     * @return \BillAndGoBundle\Entity\Tax
     */
    public function getRefTax()
    {
        return $this->refTax;
    }
}
