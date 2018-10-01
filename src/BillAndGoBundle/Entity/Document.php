<?php

/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://www.billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */


namespace BillAndGoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refUser", referencedColumnName="id")
     * })
     */
    private $refUser;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refClient", referencedColumnName="id")
     * })
     */
    private $refClient;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Line", inversedBy="refEstimate")
     * @ORM\JoinTable(name="line_estimate",
     *      joinColumns={@ORM\JoinColumn(name="estimate_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *      )
     */
    private $refLines;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Line", inversedBy="refBill")
     * @ORM\JoinTable(name="line_bill",
     *      joinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *      )
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
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Paiment", inversedBy="refBill")
     * @ORM\JoinTable(name="bill_paiment",
     *      joinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="paiment_id", referencedColumnName="id")}
     *      )
     */
    private $refPaiment;

    /**
     * @var int
     *
     * @ORM\Column(name="token", type="integer", nullable=true)
     */
    private $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->refLines = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() : int
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
    public function setNumber(string $number) : self
    {
        $this->number = trim(strip_tags(strip_tags($number)));
        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber() : ?string
    {
        return $this->number;
    }

    /**
     * Set description
     *
     * @param string|null $description
     *
     * @return Document
     */
    public function setDescription(?string $description) : self
    {
        $this->description = trim(strip_tags($description));
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() : ?string
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
    public function setStatus(string $status) : self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus() : ?string
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
    public function setSentDate(\DateTime $sentDate) : self
    {
        $this->sentDate = $sentDate;
        return $this;
    }

    /**
     * Get sentDate
     *
     * @return \DateTime
     */
    public function getSentDate() : ?\DateTime
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
    public function setDelayDate(\DateTime $delayDate) : self
    {
        $this->delayDate = $delayDate;
        return $this;
    }

    /**
     * Get delayDate
     *
     * @return \DateTime
     */
    public function getDelayDate() : ?\DateTime
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
    public function setAnswerDate(\DateTime $answerDate) : self
    {
        $this->answerDate = $answerDate;
        return $this;
    }

    /**
     * Get answerDate
     *
     * @return \DateTime
     */
    public function getAnswerDate() : ?\DateTime
    {
        return $this->answerDate;
    }

    /**
     * Set refUser
     *
     * @param User $refUser
     *
     * @return Document
     */
    public function setRefUser(?User $refUser = null) : self
    {
        $this->refUser = $refUser;
        return $this;
    }

    /**
     * Get refUser
     *
     * @return User|null
     */
    public function getRefUser() : ?User
    {
        return $this->refUser;
    }

    /**
     * Set refClient
     *
     * @param Client $refClient
     *
     * @return Document
     */
    public function setRefClient(?Client $refClient = null) : self
    {
        $this->refClient = $refClient;
        return $this;
    }

    /**
     * Get refClient
     *
     * @return Client
     */
    public function getRefClient() : ?Client
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
    public function setType(bool $type) : self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * true if estimate, false if bill
     *
     * @return bool
     */
    public function isEstimate(): bool
    {
        return $this->type;
    }

    /**
     * Add refLine
     *
     * @param Line $refLine
     *
     * @return Document
     */
    public function addRefLine(Line $refLine)
    {
        $this->refLines[] = $refLine;
        return $this;
    }

    /**
     * Remove refLine
     *
     * @param Line $refLine
     *
     * @return Document
     */
    public function removeRefLine(Line $refLine) : self
    {
        $this->refLines->removeElement($refLine);
        return $this;
    }

    /**
     * Get refLines
     *
     * @return Collection
     */
    public function getRefLines() : Collection
    {
        return $this->refLines;
    }

    /**
     * Add refLinesB
     *
     * @param Line $refLinesB
     *
     * @return Document
     */
    public function addRefLinesB(Line $refLinesB) : self
    {
        $this->refLinesB[] = $refLinesB;
        return $this;
    }

    /**
     * Remove refLinesB
     *
     * @param Line $refLinesB
     */
    public function removeRefLinesB(Line $refLinesB)
    {
        $this->refLinesB->removeElement($refLinesB);
    }

    /**
     * Get refLinesB
     *
     * @return Collection
     */
    public function getRefLinesB() : Collection
    {
        return $this->refLinesB;
    }

    /**
     * Add refPaiment
     *
     * @param Paiment $refPaiment
     *
     * @return Document
     */
    public function addRefPaiment(Paiment $refPaiment) : self
    {
        $this->refPaiment[] = $refPaiment;
        return $this;
    }

    /**
     * Remove refPaiment
     *
     * @param Paiment $refPaiment
     *
     * @return Document
     */
    public function removeRefPaiment(Paiment $refPaiment) : self
    {
        $this->refPaiment->removeElement($refPaiment);
        return $this;
    }

    /**
     * Get refPaiment
     *
     * @return Collection
     */
    public function getRefPaiment() : Collection
    {
        return $this->refPaiment;
    }

    /**
     * get sum of lines HT
     *
     * @return float
     */
    public function getHT() : float
    {
        $ht = 0;
        if ($this->type) {
            foreach ($this->refLines as $line) {
                $ht += $line->getPrice() * $line->getQuantity();
            }
        } else {
            foreach ($this->refLinesB as $line) {
                $ht += $line->getPrice() * $line->getQuantity();
            }
        }
        return $ht;
    }

    /**
     * get sum of lines VAT
     *
     * @return float
     */
    public function getVAT() : float
    {
        $vat = 0;
        if ($this->type) {
            foreach ($this->refLines as $line) {
                $vat += $line->getPrice() * $line->getQuantity() * $line->getRefTax()->getPercent() / 100;
            }
        } else {
            foreach ($this->refLinesB as $line) {
                $vat += $line->getPrice() * $line->getQuantity() * $line->getRefTax()->getPercent() / 100;
            }
        }
        return $vat;
    }

    /**
     * get sum of paiments
     *
     * @return float
     */
    public function getSumPaiments() : float
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
    public function areLinesTransformed() : bool
    {
        foreach ($this->refLines as $line) {
            if (!(isset($line->getRefBill()[0]))
                &&
                !(isset($line->getRefProject()[0]))
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * is bill paid
     * @return bool
     */
    public function isBillPaid() : bool
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
    public function setToken(int $token) : self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get token
     *
     * @return integer
     */
    public function getToken() : int
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function stringify() : string
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
                'valueHT'     => $line->getPrice() * $line->getQuantity(),
                'status'    => $line->getStatus()
            ];
        }
        foreach ($this->getRefLinesB() as $line) {
            /** @var Line $line */
            $data['lines'][$line->getId()] = [
                'id'        => $line->getId(),
                'name'      => $line->getName(),
                'valueHT'     => $line->getPrice() * $line->getQuantity(),
                'status'    => $line->getStatus()
            ];
        }

        return json_encode($data);
    }
}
