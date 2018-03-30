<?php

namespace MyJobManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillLine
 *
 * @ORM\Table(name="bill_line", indexes={@ORM\Index(name="idx_devis_line_0", columns={"ref_bill"})})
 * @ORM\Entity
 */
class BillLine
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
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
     * @ORM\Column(name="price_ht", type="float", nullable=false)
     */
    private $priceHt;

    /**
     * @var float
     *
     * @ORM\Column(name="price_ttc", type="float", nullable=false)
     */
    private $priceTtc;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \MyJobManagerBundle\Entity\Bill
     *
     * @ORM\ManyToOne(targetEntity="MyJobManagerBundle\Entity\Bill")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_bill", referencedColumnName="id")
     * })
     */
    private $refBill;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return BillLine
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
     * @return BillLine
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
     * @return BillLine
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
     * @return BillLine
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
     * @return BillLine
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
     * Set refBill
     *
     * @param \MyJobManagerBundle\Entity\Bill $refBill
     *
     * @return BillLine
     */
    public function setRefBill(\MyJobManagerBundle\Entity\Bill $refBill = null)
    {
        $this->refBill = $refBill;

        return $this;
    }

    /**
     * Get refBill
     *
     * @return \MyJobManagerBundle\Entity\Bill
     */
    public function getRefBill()
    {
        return $this->refBill;
    }
}
