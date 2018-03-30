<?php

namespace MyJobManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bill
 *
 * @ORM\Table(name="bill", uniqueConstraints={@ORM\UniqueConstraint(name="pk_bill", columns={"id"})}, indexes={@ORM\Index(name="idx_bill", columns={"ref_user"})})
 * @ORM\Entity
 */
class Bill
{
    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=20, nullable=false)
     */
    private $number;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation", type="datetime", nullable=false)
     */
    private $creation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paiment_date", type="datetime", nullable=false)
     */
    private $paimentDate;

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
     * @var \MyJobManagerBundle\Entity\Project
     *
     * @ORM\ManyToMany(targetEntity="MyJobManagerBundle\Entity\Project", mappedBy="refBill")
     * @ORM\JoinTable(name="project_bill",
     *		joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")}
     *		)
     */
    private $refProject;

    /**
     * @var \MyJobManagerBundle\Entity\Devis
     *
     * @ORM\ManyToMany(targetEntity="MyJobManagerBundle\Entity\Devis", inversedBy="refBill")
     * @ORM\JoinTable(name="estimate_bill",
     *		joinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="estimate_id", referencedColumnName="id")}
     *		)
     */
    private $refEstimate;

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
     * @var \MyJobManagerBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="MyJobManagerBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client", referencedColumnName="id")
     * })
     */
    private $client;

    /**
     * @ORM\ManyToMany(targetEntity="BillLine", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="bill_billline",
     *		joinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="billline_id", referencedColumnName="id", unique=true)}
     *		)
     */
    private $lines;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sendTime", type="datetime", nullable=true)
     */
    private $sendTime;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = 'draft';

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Bill
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
     * Set creation
     *
     * @param \DateTime $creation
     *
     * @return Bill
     */
    public function setCreation($creation)
    {
        $this->creation = $creation;

        return $this;
    }

    /**
     * Get creation
     *
     * @return \DateTime
     */
    public function getCreation()
    {
        return $this->creation;
    }

    /**
     * Set paimentDate
     *
     * @param \DateTime $paimentDate
     *
     * @return Bill
     */
    public function setPaimentDate($paimentDate)
    {
        $this->paimentDate = $paimentDate;

        return $this;
    }

    /**
     * Get paimentDate
     *
     * @return \DateTime
     */
    public function getPaimentDate()
    {
        return $this->paimentDate;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Bill
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
     * Set refProject
     *
     * @param \MyJobManagerBundle\Entity\Project $refProject
     *
     * @return Bill
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
     * Set refUser
     *
     * @param \MyJobManagerBundle\Entity\User $refUser
     *
     * @return Bill
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
     * Set client
     *
     * @param \MyJobManagerBundle\Entity\Client $client
     *
     * @return Bill
     */
    public function setClient(\MyJobManagerBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \MyJobManagerBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add line
     *
     * @param \MyJobManagerBundle\Entity\BillLine $line
     *
     * @return Bill
     */
    public function addLine(\MyJobManagerBundle\Entity\BillLine $line)
    {
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Remove line
     *
     * @param \MyJobManagerBundle\Entity\BillLine $line
     */
    public function removeLine(\MyJobManagerBundle\Entity\BillLine $line)
    {
        $this->lines->removeElement($line);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Set sendTime
     *
     * @param \DateTime $sendTime
     *
     * @return Bill
     */
    public function setSendTime($sendTime)
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    /**
     * Get sendTime
     *
     * @return \DateTime
     */
    public function getSendTime()
    {
        return $this->sendTime;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Bill
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
     * Get refEstimate
     *
     * @return \MyJobManagerBundle\Entity\Devis
     */
    public function getRefEstimate()
    {
        return $this->refEstimate;
    }

    /**
     * Add refEstimate
     *
     * @param \MyJobManagerBundle\Entity\Devis $refEstimate
     *
     * @return Bill
     */
    public function addRefEstimate(\MyJobManagerBundle\Entity\Devis $refEstimate)
    {
        $this->refEstimate[] = $refEstimate;

        return $this;
    }

    /**
     * Remove refEstimate
     *
     * @param \MyJobManagerBundle\Entity\Devis $refEstimate
     */
    public function removeRefEstimate(\MyJobManagerBundle\Entity\Devis $refEstimate)
    {
        $this->refEstimate->removeElement($refEstimate);
    }

    /**
     * Add refProject
     *
     * @param \MyJobManagerBundle\Entity\Project $refProject
     *
     * @return Bill
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
     * Add refPaiment
     *
     * @param \MyJobManagerBundle\Entity\Paiment $refPaiment
     *
     * @return Bill
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
     * sum paiments
     * @return float
     */
    public function getSumPaiments()
    {
        $sum = 0;
        foreach ($this->refPaiment as $paiment)
        {
            $sum += $paiment->getAmount();
        }
        return $sum;
    }

    /**
     * sum lines TTC
     * @return float
     */
    public function getSumTTC()
    {
        $sum = 0;
        foreach ($this->lines as $line)
        {
            $sum += $line->getPriceTTC() * $line->getQuantity();
        }
        return $sum;
    }
}
