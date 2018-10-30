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
 * Document is either a bill or an estimate, depending of its type.
 * It can be seen as a collection of Lines, with meta-infos.
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="BillAndGoBundle\Repository\DocumentRepository")
 */
class Document
{
    /**
     * The internal id of the document.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The user who created the document. He is the only one who can see it, edit it, use it.
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refUser", referencedColumnName="id")
     * })
     */
    private $refUser;

    /**
     * The client for who the document is. The document can then be sent to one of the client's contact.
     *
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refClient", referencedColumnName="id")
     * })
     */
    private $refClient;

    /**
     * The collection of Lines is the core of the document.
     * Each Line represents an item estimated/billed.
     * This field is used if the document is an estimate.
     *
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
     * The collection of Lines is the core of the document.
     * Each Line represents an item estimated/billed.
     * This field is used if the document is a bill.
     *
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
     * The official number of the document, used for external/legal reference.
     * It is auto-generated for legal motives : a user's bills must have bills with successive numbers.
     *
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * Since a document can be either a bill or an estimate, a boolean has been used.
     * True if estimate, false if bill.
     *
     * @var bool
     *
     * @ORM\Column(name="type", type="boolean")
     */
    private $type;

    /**
     * The description of the document, wich will appear on the pdf.
     *
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * The status of the document. It can be :
     * * draw : the document is a draw, has not been sent and can be edited. Default status on creation.
     * * canceled : the document has been canceled.
     * * estimated : the estimate has been sent to the client and is waiting for response.
     * * refused : the estimate has been sent to the client, who has declined it.
     * * accepted : the estimate has been sent to the client, who has accepted it.
     * * billed : the bill has been sent to the client and is wainting for paiment.
     * * partially : the bill has been sent to the client and has been partially paid.
     * * paid : the bill has been sent to the client and has been fully paid.
     *
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * The date on which the document has been sent to the client contact.
     * Null it has not been sent.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="sentDate", type="date", nullable=true)
     */
    private $sentDate;


    /**
     * The date on which the document has been created.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="creaationDate", type="date", nullable=true)
     */
    private $creationDate;

    /**
     * The date on which the estimate will be seen as refused if not accepted,
     * or the bill seen as late if not totally paid.
     * Set upon creation, by default at the end of the next month.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="delayDate", type="date", nullable=true)
     */
    private $delayDate;

    /**
     * The date on which the estimate has been accepted or refused by the client contact.
     * Null if no answer.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="answerDate", type="date", nullable=true)
     */
    private $answerDate;


    /**
     * The date on which the estimate has been accepted  by the client contact.
     * Null if no answer.
     *
     * @var \DateTime|null
     *
     * @ORM\Column(name="answerDateAccepted", type="datetime", nullable=true)
     */
    private $answerDateAccepted;


    /**
     * The date on which the estimate has been  refused by the client contact.
     * Null if no answer.
     *
     * @var \DateTime|null
     *
     * @ORM\Column(name="answerDateRefused", type="datetime", nullable=true)
     */
    private $answerDateRefused;



    /**
     * @var string|null
     *
     * @ORM\Column(name="paymentCondition", type="text", nullable=true)
     */
    private $paymentCondition;


    /**
     * @var string|null
     *
     * @ORM\Column(name="makeCondition", type="text", nullable=true)
     */
    private $makeCondition;


    /**
     * @var string|null
     *
     * @ORM\Column(name="specCondition", type="text", nullable=true)
     */
    private $specCondition;



    /**
     * All paiments linked to the bill.
     * Used to know if the bill is paid.
     *
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
     * Random token used for secure communication with the client contact
     * who will be able to accept or refuse an estimate from the mail.
     *
     * @var int
     *
     * @ORM\Column(name="token", type="integer", nullable=true)
     */
    private $token;

    /**
     * Constructor
     * sets an empty ArrayCollection for reflines.
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
     * Set number after removing tags
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
     * Set description after removing tags
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
     * Set status
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
     * @return \DateTime|null
     */
    public function getAnswerDateAccepted(): ?\DateTime
    {
        return $this->answerDateAccepted;
    }

    /**
     * @param \DateTime|null $answerDateAccepted
     */
    public function setAnswerDateAccepted(?\DateTime $answerDateAccepted): void
    {
        $this->answerDateAccepted = $answerDateAccepted;
    }

    /**
     * @return \DateTime|null
     */
    public function getAnswerDateRefused(): ?\DateTime
    {
        return $this->answerDateRefused;
    }

    /**
     * @param \DateTime|null $answerDateRefused
     */
    public function setAnswerDateRefused(?\DateTime $answerDateRefused): void
    {
        $this->answerDateRefused = $answerDateRefused;
    }




    /**
     * Set sentDate
     *
     * @param \DateTime $sentDate
     *
     * @return Document
     */
    public function setSentDate(?\DateTime $sentDate) : self
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
     * Add a Line to refLine, the collection of estimate lines
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
     * Remove a Line from refLine if it is found.
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
     * Get refLines, Lines of an estimate
     *
     * @return Collection
     */
    public function getRefLines() : Collection
    {
        return $this->refLines;
    }

    /**
     * Add a Line to refLinesB, the collection of bill Lines.
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
     * Remove a Line from refLinesB if it is found
     *
     * @param Line $refLinesB
     */
    public function removeRefLinesB(Line $refLinesB)
    {
        $this->refLinesB->removeElement($refLinesB);
    }

    /**
     * Get refLinesB, the collection of bill Lines 
     *
     * @return Collection
     */
    public function getRefLinesB() : Collection
    {
        return $this->refLinesB;
    }

    /**
     * Link a Paiment to the document.
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
     * Remove a paiment if found.
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
     * Get a collection of paiments linked to the document.
     *
     * @return Collection
     */
    public function getRefPaiment() : Collection
    {
        return $this->refPaiment;
    }

    /**
     * get sum of lines HT.
     * 0 if no line.
     * sum the refLines if estimate, the refLinesB if bill.
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
     * 0 if no line.
     * sum the refLines if estimate, the refLinesB if bill.
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
     * get sum of paiments linked to the document
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
     * Are all lines transformed in project or bills ?
     * Checks for each estimat eline if there is a project or a bill linked to this line.
     *
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
     * Is bill paid ?
     * Checks if the sum of Paiments is equal or above the TTC price of the bill.
     * 
     * @return bool
     */
    public function isBillPaid() : bool
    {
        return ($this->getSumPaiments() >= $this->getHT() + $this->getVAT());
    }


    /**
     * Set token.
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
     * @return null|string
     */
    public function getPaymentCondition(): ?string
    {
        return $this->paymentCondition;
    }

    /**
     * @param null|string $paymentCondition
     */
    public function setPaymentCondition(?string $paymentCondition): void
    {
        $this->paymentCondition = $paymentCondition;
    }

    /**
     * @return null|string
     */
    public function getMakeCondition(): ?string
    {
        return $this->makeCondition;
    }

    /**
     * @param null|string $makeCondition
     */
    public function setMakeCondition(?string $makeCondition): void
    {
        $this->makeCondition = $makeCondition;
    }

    /**
     * @return null|string
     */
    public function getSpecCondition(): ?string
    {
        return $this->specCondition;
    }

    /**
     * @param null|string $specCondition
     */
    public function setSpecCondition(?string $specCondition): void
    {
        $this->specCondition = $specCondition;
    }


    /**
     * @return \DateTime
     */
    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate(\DateTime $creationDate): void
    {
        $this->creationDate = $creationDate;
    }



    /**
     * Returns a json built with the document data and its Lines data.
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
