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
 * Paiment
 *
 * @ORM\Table(name="paiment", indexes={@ORM\Index(name="idx_paiment", columns={"ref_user"})})
 * @ORM\Entity
 */
class Paiment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_paiment", type="date", nullable=true)
     */
    private $datePaiment;

    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", nullable=false)
     */
    private $mode;

    /**
     * @var \BillAndGoBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="BillAndGoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_user", referencedColumnName="id")
     * })
     */
    private $refUser;

    /**
     * @var \BillAndGoBundle\Entity\Document
     *
     * @ORM\ManyToMany(targetEntity="BillAndGoBundle\Entity\Document", mappedBy="refPaiment")
     * @ORM\JoinTable(name="bill_paiment",
     *		joinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="paiment_id", referencedColumnName="id")}
     *		)
     */
    private $refBill;





    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Paiment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set datePaiment
     *
     * @param \DateTime $datePaiment
     *
     * @return Paiment
     */
    public function setDatePaiment($datePaiment)
    {
        $this->datePaiment = $datePaiment;

        return $this;
    }

    /**
     * Get datePaiment
     *
     * @return \DateTime
     */
    public function getDatePaiment()
    {
        return $this->datePaiment;
    }

    /**
     * Set mode
     *
     * @param string $mode
     *
     * @return Paiment
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
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
     * @return Paiment
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
     * Constructor
     */
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->refBill = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add refBill
     *
     * @param \BillAndGoBundle\Entity\Document $refBill
     *
     * @return Paiment
     */
    public function addRefBill(\BillAndGoBundle\Entity\Document $refBill)
    {
        $this->refBill[] = $refBill;

        return $this;
    }

    /**
     * alias of addrefBill
     *
     * @param \BillAndGoBundle\Entity\Document $refBill
     *
     * @return Paiment
     */
    public function setRefBill(\BillAndGoBundle\Entity\Document $refBill)
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
}
