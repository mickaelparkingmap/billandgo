<?php

namespace MyJobManagerBundle\Entity;

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
     * @var \MyJobManagerBundle\Entity\ClientContact
     *
     * @ORM\ManyToOne(targetEntity="MyJobManagerBundle\Entity\ClientContact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_contact", referencedColumnName="id")
     * })
     */
    private $refContact;

    /**
     * @var \MyJobManagerBundle\Entity\Devis
     *
     * @ORM\ManyToOne(targetEntity="MyJobManagerBundle\Entity\Devis")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_devis", referencedColumnName="id")
     * })
     */
    private $refDevis;



    /**
     * Set creation
     *
     * @param \DateTime $creation
     *
     * @return Note
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
     * Set name
     *
     * @param string $name
     *
     * @return Note
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
     * @return Note
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
     * Set refContact
     *
     * @param \MyJobManagerBundle\Entity\ClientContact $refContact
     *
     * @return Note
     */
    public function setRefContact(\MyJobManagerBundle\Entity\ClientContact $refContact = null)
    {
        $this->refContact = $refContact;

        return $this;
    }

    /**
     * Get refContact
     *
     * @return \MyJobManagerBundle\Entity\ClientContact
     */
    public function getRefContact()
    {
        return $this->refContact;
    }

    /**
     * Set refDevis
     *
     * @param \MyJobManagerBundle\Entity\Devis $refDevis
     *
     * @return Note
     */
    public function setRefDevis(\MyJobManagerBundle\Entity\Devis $refDevis = null)
    {
        $this->refDevis = $refDevis;

        return $this;
    }

    /**
     * Get refDevis
     *
     * @return \MyJobManagerBundle\Entity\Devis
     */
    public function getRefDevis()
    {
        return $this->refDevis;
    }
}
