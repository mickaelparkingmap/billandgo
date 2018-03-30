<?php

namespace MyJobManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Todo
 *
 * @ORM\Table(name="todo", indexes={@ORM\Index(name="idx_todo", columns={"ref_project"})})
 * @ORM\Entity
 */
class Todo
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="estimated_time", type="float", nullable=false)
     */
    private $estimatedTime;

    /**
     * @var float
     *
     * @ORM\Column(name="price_ht", type="float", nullable=true)
     */
    private $priceHt;

    /**
     * @var float
     *
     * @ORM\Column(name="price_ttc", type="float", nullable=true)
     */
    private $priceTtc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = 'planned';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \MyJobManagerBundle\Entity\Project
     *
     * @ORM\ManyToOne(targetEntity="MyJobManagerBundle\Entity\Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_project", referencedColumnName="id")
     * })
     */
    private $refProject;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Todo
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
     * @return Todo
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
     * Set estimatedTime
     *
     * @param float $estimatedTime
     *
     * @return Todo
     */
    public function setEstimatedTime($estimatedTime)
    {
        $this->estimatedTime = $estimatedTime;

        return $this;
    }

    /**
     * Get estimatedTime
     *
     * @return float
     */
    public function getEstimatedTime()
    {
        return $this->estimatedTime;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return Todo
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
     * Set status
     *
     * @param string $status
     *
     * @return Todo
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set refProject
     *
     * @param \MyJobManagerBundle\Entity\Project $refProject
     *
     * @return Todo
     */
    public function setRefProject(\MyJobManagerBundle\Entity\Project $refProject = null)
    {
        $this->refProject = $refProject;

        return $this;
    }

    /**
     * Get refProject
     *
     * @return \MyJobManagerBundle\Entity\Project
     */
    public function getRefProject()
    {
        return $this->refProject;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Todo
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set priceHt
     *
     * @param float $priceHt
     *
     * @return Todo
     */
    public function setPriceHt($priceHt)
    {
        $this->priceHt = $priceHt;

        return $this;
    }

    /**
     * Get priceHt
     *
     * @return float
     */
    public function getPriceHt()
    {
        return $this->priceHt;
    }

    /**
     * Set priceTtc
     *
     * @param float $priceTtc
     *
     * @return Todo
     */
    public function setPriceTtc($priceTtc)
    {
        $this->priceTtc = $priceTtc;

        return $this;
    }

    /**
     * Get priceTtc
     *
     * @return float
     */
    public function getPriceTtc()
    {
        return $this->priceTtc;
    }
}
