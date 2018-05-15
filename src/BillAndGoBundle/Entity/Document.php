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
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="BillAndGoBundle\Repository\DocumentRepository")
 */
class Document
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
     * @var \BillAndGoBundle\Entity\Line
     *
     * @ORM\ManyToMany(targetEntity="BillAndGoBundle\Entity\Line", inversedBy="refEstimate")
     * @ORM\JoinTable(name="line_estimate",
     *		joinColumns={@ORM\JoinColumn(name="estimate_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refLines;

    /**
     * @var \BillAndGoBundle\Entity\Line
     *
     * @ORM\ManyToMany(targetEntity="BillAndGoBundle\Entity\Line", inversedBy="refBill")
     * @ORM\JoinTable(name="line_bill",
     *		joinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refLinesB;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @var bool
     *
     * @ORM\Column(name="type", type="boolean")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sentDate", type="date", nullable=true)
     */
    private $sentDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delayDate", type="date", nullable=true)
     */
    private $delayDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="answerDate", type="date", nullable=true)
     */
    private $answerDate;

    /**
     * @var \BillAndGoBundle\Entity\Paiment
     *
     * @ORM\ManyToMany(targetEntity="BillAndGoBundle\Entity\Paiment", inversedBy="refBill")
     * @ORM\JoinTable(name="bill_paiment",
     *		joinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="paiment_id", referencedColumnName="id")}
     *		)
     */
    private $refPaiment;

    /**
     * @var int
     *
     * @ORM\Column(name="token", type="integer", nullable=true)
     */
    private $token;

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
     * Set number
     *
     * @param string $number
     *
     * @return Document
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Document
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
     * Set us
     *
     * @param string $status
     *
     * @return Document
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
     * Set sentDate
     *
     * @param \DateTime $sentDate
     *
     * @return Document
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    /**
     * Get sentDate
     *
     * @return \DateTime
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * Set delayDate
     *
     * @param \DateTime $delayDate
     *
     * @return Document
     */
    public function setDelayDate($delayDate)
    {
        $this->delayDate = $delayDate;

        return $this;
    }

    /**
     * Get delayDate
     *
     * @return \DateTime
     */
    public function getDelayDate()
    {
        return $this->delayDate;
    }

    /**
     * Set answerDate
     *
     * @param \DateTime $answerDate
     *
     * @return Document
     */
    public function setAnswerDate($answerDate)
    {
        $this->answerDate = $answerDate;

        return $this;
    }

    /**
     * Get answerDate
     *
     * @return \DateTime
     */
    public function getAnswerDate()
    {
        return $this->answerDate;
    }

    /**
     * Set refUser
     *
     * @param \BillAndGoBundle\Entity\User $refUser
     *
     * @return Document
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
     * @return Document
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
     * Set type
     *
     * @param boolean $type
     *
     * @return Document
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->refLines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add refLine
     *
     * @param \BillAndGoBundle\Entity\Line $refLine
     *
     * @return Document
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

    /**
     * Add refLinesB
     *
     * @param \BillAndGoBundle\Entity\Line $refLinesB
     *
     * @return Document
     */
    public function addRefLinesB(\BillAndGoBundle\Entity\Line $refLinesB)
    {
        $this->refLinesB[] = $refLinesB;

        return $this;
    }

    /**
     * Remove refLinesB
     *
     * @param \BillAndGoBundle\Entity\Line $refLinesB
     */
    public function removeRefLinesB(\BillAndGoBundle\Entity\Line $refLinesB)
    {
        $this->refLinesB->removeElement($refLinesB);
    }

    /**
     * Get refLinesB
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRefLinesB()
    {
        return $this->refLinesB;
    }

    /**
     * Add refPaiment
     *
     * @param \BillAndGoBundle\Entity\Paiment $refPaiment
     *
     * @return Document
     */
    public function addRefPaiment(\BillAndGoBundle\Entity\Paiment $refPaiment)
    {
        $this->refPaiment[] = $refPaiment;

        return $this;
    }

    /**
     * Remove refPaiment
     *
     * @param \BillAndGoBundle\Entity\Paiment $refPaiment
     */
    public function removeRefPaiment(\BillAndGoBundle\Entity\Paiment $refPaiment)
    {
        $this->refPaiment->removeElement($refPaiment);
    }

    /**
     * Get refPaiment
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRefPaiment()
    {
        return $this->refPaiment;
    }

    /**
     * get sum of lines HT
     *
     * @return float
     */
    public function getHT()
    {
        $ht = 0;
        if ($this->type) {
            foreach ($this->refLines as $line) {
                $ht += $line->getPrice() * $line->getQuantity();
            }
        }
        else
            foreach ($this->refLinesB as $line) {
                $ht += $line->getPrice() * $line->getQuantity();
            }
        return $ht;
    }

    /**
     * get sum of lines VAT
     *
     * @return float
     */
    public function getVAT()
    {
        $vat = 0;
        if ($this->type) {
            foreach ($this->refLines as $line) {
                $vat += $line->getPrice() * $line->getQuantity() * $line->getRefTax()->getPercent() / 100;
            }
        }
        else
            foreach ($this->refLinesB as $line) {
                $vat += $line->getPrice() * $line->getQuantity() * $line->getRefTax()->getPercent() / 100;
            }
        return $vat;
    }

    /**
     * get sum of paiments
     *
     * @return float
     */
    public function getSumPaiments()
    {
        $sum = 0;
        foreach ($this->refPaiment as $paiment) {
            $sum += $paiment->getAmount();
        }
        return $sum;
    }

    /**
     * are all lines transformed in project or bills ?
     * @return bool
     */
    public function areLinesTransformed()
    {
        foreach ($this->refLines as $line) {
            if (
                !(isset($line->getRefBill()[0]))
                &&
                !(isset($line->getRefProject()[0]))
            )
            {
                return false;
            }
        }
        return true;
    }

    /**
     * is bill paid
     * @return bool
     */
    public function isBillPaid()
    {
        return ($this->getSumPaiments() >= $this->getHT() + $this->getVAT());
    }


    /**
     * Set token
     *
     * @param integer $token
     *
     * @return Document
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return integer
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function __toString () : string
    {
        $data = [
            'id'            => $this->id,
            'number'        => $this->number,
            'type'          => ($this->type) ? 'devis' : 'facture',
            'description'   => $this->description,
            'status'        => $this->status,
            'client'        => $this->refClient->getCompanyName(),
            'clientId'      => $this->refClient->getId(),
            'lines'         => []
        ];
        if (null !== $this->sentDate) {
            $data['sentDate'] = $this->sentDate->format('y-m-d H:i:s');
        }
        if (null !== $this->delayDate) {
            $data['delayDate'] = $this->delayDate->format('y-m-d H:i:s');
        }
        if (null !== $this->answerDate) {
            $data['answerDate'] = $this->answerDate->format('y-m-d H:i:s');
        }

        foreach ($this->getRefLines() as $line) {
            /** @var Line $line */
            $data['lines'][$line->getId()] = [
                'id'        => $line->getId(),
                'name'      => $line->getName(),
                'value'     => $line->getPrice() * $line->getQuantity(),
                'status'    => $line->getStatus()
            ];
        }

        return json_encode($data);
    }


}
