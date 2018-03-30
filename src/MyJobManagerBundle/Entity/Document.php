<?php

namespace MyJobManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="MyJobManagerBundle\Repository\DocumentRepository")
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
     * @var \MyJobManagerBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="MyJobManagerBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refUser", referencedColumnName="id")
     * })
     */
    private $refUser;

    /**
     * @var \MyJobManagerBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="MyJobManagerBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refClient", referencedColumnName="id")
     * })
     */
    private $refClient;

    /**
     * @var \MyJobManagerBundle\Entity\Line
     *
     * @ORM\ManyToMany(targetEntity="MyJobManagerBundle\Entity\Line", inversedBy="refEstimate")
     * @ORM\JoinTable(name="line_estimate",
     *		joinColumns={@ORM\JoinColumn(name="estimate_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refLines;

    /**
     * @var \MyJobManagerBundle\Entity\Line
     *
     * @ORM\ManyToMany(targetEntity="MyJobManagerBundle\Entity\Line", inversedBy="refBill")
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
     * @var \MyJobManagerBundle\Entity\Paiment
     *
     * @ORM\ManyToMany(targetEntity="MyJobManagerBundle\Entity\Paiment", inversedBy="refBill")
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
     * @param \MyJobManagerBundle\Entity\User $refUser
     *
     * @return Document
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

    /**
     * Set refClient
     *
     * @param \MyJobManagerBundle\Entity\Client $refClient
     *
     * @return Document
     */
    public function setRefClient(\MyJobManagerBundle\Entity\Client $refClient = null)
    {
        $this->refClient = $refClient;

        return $this;
    }

    /**
     * Get refClient
     *
     * @return \MyJobManagerBundle\Entity\Client
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
     * Add refProject
     *
     * @param \MyJobManagerBundle\Entity\Project $refProject
     *
     * @return Document
     */
    public function addRefProject(\MyJobManagerBundle\Entity\Project $refProject)
    {
        $this->refProject[] = $refProject;

        return $this;
    }

    /**
     * Remove refProject
     *
     * @param \MyJobManagerBundle\Entity\Project $refProject
     */
    public function removeRefProject(\MyJobManagerBundle\Entity\Project $refProject)
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
     * Add refLine
     *
     * @param \MyJobManagerBundle\Entity\Line $refLine
     *
     * @return Document
     */
    public function addRefLine(\MyJobManagerBundle\Entity\Line $refLine)
    {
        $this->refLines[] = $refLine;

        return $this;
    }

    /**
     * Remove refLine
     *
     * @param \MyJobManagerBundle\Entity\Line $refLine
     */
    public function removeRefLine(\MyJobManagerBundle\Entity\Line $refLine)
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
     * @param \MyJobManagerBundle\Entity\Line $refLinesB
     *
     * @return Document
     */
    public function addRefLinesB(\MyJobManagerBundle\Entity\Line $refLinesB)
    {
        $this->refLinesB[] = $refLinesB;

        return $this;
    }

    /**
     * Remove refLinesB
     *
     * @param \MyJobManagerBundle\Entity\Line $refLinesB
     */
    public function removeRefLinesB(\MyJobManagerBundle\Entity\Line $refLinesB)
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
     * Add refBill
     *
     * @param \MyJobManagerBundle\Entity\Document $refBill
     *
     * @return Document
     */
    public function addRefBill(\MyJobManagerBundle\Entity\Document $refBill)
    {
        $this->refBill[] = $refBill;

        return $this;
    }

    /**
     * Remove refBill
     *
     * @param \MyJobManagerBundle\Entity\Document $refBill
     */
    public function removeRefBill(\MyJobManagerBundle\Entity\Document $refBill)
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
     * Add refEstimate
     *
     * @param \MyJobManagerBundle\Entity\Document $refEstimate
     *
     * @return Document
     */
    public function addRefEstimate(\MyJobManagerBundle\Entity\Document $refEstimate)
    {
        $this->refEstimate[] = $refEstimate;

        return $this;
    }

    /**
     * Remove refEstimate
     *
     * @param \MyJobManagerBundle\Entity\Document $refEstimate
     */
    public function removeRefEstimate(\MyJobManagerBundle\Entity\Document $refEstimate)
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
     * Add refPaiment
     *
     * @param \MyJobManagerBundle\Entity\Paiment $refPaiment
     *
     * @return Document
     */
    public function addRefPaiment(\MyJobManagerBundle\Entity\Paiment $refPaiment)
    {
        $this->refPaiment[] = $refPaiment;

        return $this;
    }

    /**
     * Remove refPaiment
     *
     * @param \MyJobManagerBundle\Entity\Paiment $refPaiment
     */
    public function removeRefPaiment(\MyJobManagerBundle\Entity\Paiment $refPaiment)
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
}
