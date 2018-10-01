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

use Doctrine\ORM\Mapping as ORM;

/**
 * Note
 *
 * @ORM\Table(name="note", indexes={@ORM\Index(name="idx_note", columns={"ref_contact"}), @ORM\Index(name="idx_note_0", columns={"ref_devis"})})
 * @ORM\Entity
 */
class Note
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation", type="datetime", nullable=false)
     */
    private $creation = 'CURRENT_TIMESTAMP';

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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var ClientContact
     *
     * @ORM\ManyToOne(targetEntity="ClientContact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_contact", referencedColumnName="id")
     * })
     */
    private $refContact;

    /**
     * @var Document
     *
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_devis", referencedColumnName="id")
     * })
     */
    private $refDocument;



    /**
     * Set creation
     *
     * @param \DateTime|null $creation
     *
     * @return Note
     */
    public function setCreation(?\DateTime $creation) : self
    {
        $this->creation = $creation;
        return $this;
    }

    /**
     * Get creation
     *
     * @return \DateTime|null
     */
    public function getCreation() : ?\DateTime
    {
        return $this->creation;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Note
     */
    public function setName(string $name) : self
    {
        $this->name = trim(strip_tags($name));
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Note
     */
    public function setDescription(string $description) : self
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
     * Get id
     *
     * @return integer
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set refContact
     *
     * @param ClientContact $refContact
     *
     * @return Note
     */
    public function setRefContact(?ClientContact $refContact = null) : self
    {
        $this->refContact = $refContact;
        return $this;
    }

    /**
     * Get refContact
     *
     * @return ClientContact|null
     */
    public function getRefContact() : ?ClientContact
    {
        return $this->refContact;
    }

    /**
     * Set refDevis
     *
     * @param Document|null $refDevis
     *
     * @return Note
     */
    public function setRefDocument(?Document $refDocument = null) : self
    {
        $this->refDocument = $refDocument;
        return $this;
    }

    /**
     * Get refDevis
     *
     * @return Document|null
     */
    public function getRefDocument() : ?Document
    {
        return $this->refDocument;
    }
}
