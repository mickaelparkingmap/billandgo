<?php

namespace MyJobManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Numerotation
 *
 * @ORM\Table(name="numerotation")
 * @ORM\Entity(repositoryClass="MyJobManagerBundle\Repository\NumerotationRepository")
 */
class Numerotation
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
     * @var \MyJobManagerBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="MyJobManagerBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_user", referencedColumnName="id")
     * })
     */
    private $refUser;

    /**
     * @var int
     *
     * @ORM\Column(name="EstimateIndex", type="integer", nullable=true)
     */
    private $estimateIndex;

    /**
     * @var int
     *
     * @ORM\Column(name="EstimateYearMonth", type="integer", nullable=true)
     */
    private $estimateYearMonth;

    /**
     * @var int
     *
     * @ORM\Column(name="BillIndex", type="integer", nullable=true)
     */
    private $billIndex;

    /**
     * @var int
     *
     * @ORM\Column(name="BillYearMonth", type="integer", nullable=true)
     */
    private $billYearMonth;


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
     * Set estimateIndex
     *
     * @param integer $estimateIndex
     *
     * @return Numerotation
     */
    public function setEstimateIndex($estimateIndex)
    {
        $this->estimateIndex = $estimateIndex;

        return $this;
    }

    /**
     * Get estimateIndex
     *
     * @return int
     */
    public function getEstimateIndex()
    {
        return $this->estimateIndex;
    }

    /**
     * Set estimateYearMonth
     *
     * @param integer $estimateYearMonth
     *
     * @return Numerotation
     */
    public function setEstimateYearMonth($estimateYearMonth)
    {
        $this->estimateYearMonth = $estimateYearMonth;

        return $this;
    }

    /**
     * Get estimateYearMonth
     *
     * @return int
     */
    public function getEstimateYearMonth()
    {
        return $this->estimateYearMonth;
    }

    /**
     * Set billIndex
     *
     * @param integer $billIndex
     *
     * @return Numerotation
     */
    public function setBillIndex($billIndex)
    {
        $this->billIndex = $billIndex;

        return $this;
    }

    /**
     * Get billIndex
     *
     * @return int
     */
    public function getBillIndex()
    {
        return $this->billIndex;
    }

    /**
     * Set billYearMonth
     *
     * @param integer $billYearMonth
     *
     * @return Numerotation
     */
    public function setBillYearMonth($billYearMonth)
    {
        $this->billYearMonth = $billYearMonth;

        return $this;
    }

    /**
     * Get billYearMonth
     *
     * @return int
     */
    public function getBillYearMonth()
    {
        return $this->billYearMonth;
    }

    /**
     * Set refUser
     *
     * @param \MyJobManagerBundle\Entity\User $refUser
     *
     * @return Numerotation
     */
    public function setRefUser(\MyJobManagerBundle\Entity\User $refUser = null)
    {
        $this->refUser = $refUser;

        return $this;
    }

    /**
     * Get refUser
     *
     * @return \MyJobManagerBundle\Entity\User
     */
    public function getRefUser()
    {
        return $this->refUser;
    }
}
