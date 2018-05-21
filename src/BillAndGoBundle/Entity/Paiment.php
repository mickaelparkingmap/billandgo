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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_user", referencedColumnName="id")
     * })
     */
    private $refUser;

    /**
     * @var Document
     *
     * @ORM\ManyToMany(targetEntity="Document", mappedBy="refPaiment")
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
    public function setAmount(int $amount) : self
    {
        if ($amount >= 0) {
            $this->amount = $amount;
        }
        return $this;
    }

    /**
     * Get amount
     *
     * @return integer|null
     */
    public function getAmount() : ?int
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
    public function setDatePaiment(?\DateTime $datePaiment) : self
    {
        $this->datePaiment = $datePaiment;
        return $this;
    }

    /**
     * Get datePaiment
     *
     * @return \DateTime
     */
    public function getDatePaiment() : ?\DateTime
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
    public function setMode(string $mode) : self
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Get mode
     *
     * @return string|null
     */
    public function getMode() : ?string
    {
        return $this->mode;
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
     * @return Paiment
     */
    public function setRefUser(?User $refUser = null) : self
    {
        $this->refUser = $refUser;
        return $this;
    }

    /**
     * Get refUser
     *
     * @return User
     */
    public function getRefUser() : User
    {
        return $this->refUser;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->refBill = new ArrayCollection();
    }

    /**
     * Add refBill
     *
     * @param Document $refBill
     *
     * @return Paiment
     */
    public function addRefBill(Document $refBill) : self
    {
        $this->refBill->add($refBill);
        return $this;
    }

    /**
     * alias of addrefBill
     *
     * @param Document $refBill
     *
     * @return Paiment
     */
    public function setRefBill(Document $refBill) : self
    {
        return $this->addRefBill($refBill);
    }

    /**
     * Remove refBill
     *
     * @param Document $refBill
     *
     * @return Paiment
     */
    public function removeRefBill(Document $refBill) : self
    {
        $this->refBill->removeElement($refBill);
        return $this;
    }

    /**
     * Get refBill
     *
     * @return Collection
     */
    public function getRefBill() : Collection
    {
        return $this->refBill;
    }
}
