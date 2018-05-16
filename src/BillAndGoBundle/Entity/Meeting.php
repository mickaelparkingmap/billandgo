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
 * Meeting
 *
 * @ORM\Table(name="meeting", indexes={@ORM\Index(name="idx_meeting", columns={"ref_devis"}), @ORM\Index(name="idx_meeting_0", columns={"ref_contact"})})
 * @ORM\Entity
 */
class Meeting
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="beginning_time", type="datetime", nullable=false)
     */
    private $beginningTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ending_time", type="datetime", nullable=false)
     */
    private $endingTime;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=255, nullable=true)
     */
    private $place;

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
     *   @ORM\JoinColumn(name="ref_document", referencedColumnName="id")
     * })
     */
    private $refDocument;



    /**
     * Set beginningTime
     *
     * @param \DateTime $beginningTime
     *
     * @return Meeting
     */
    public function setBeginningTime($beginningTime)
    {
        $this->beginningTime = $beginningTime;

        return $this;
    }

    /**
     * Get beginningTime
     *
     * @return \DateTime
     */
    public function getBeginningTime()
    {
        return $this->beginningTime;
    }

    /**
     * Set endingTime
     *
     * @param \DateTime $endingTime
     *
     * @return Meeting
     */
    public function setEndingTime($endingTime)
    {
        $this->endingTime = $endingTime;

        return $this;
    }

    /**
     * Get endingTime
     *
     * @return \DateTime
     */
    public function getEndingTime()
    {
        return $this->endingTime;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Meeting
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
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
     * @param ClientContact $refContact
     *
     * @return Meeting
     */
    public function setRefContact(ClientContact $refContact = null)
    {
        $this->refContact = $refContact;

        return $this;
    }

    /**
     * Get refContact
     *
     * @return ClientContact
     */
    public function getRefContact()
    {
        return $this->refContact;
    }

    /**
     * Set refDocument
     *
     * @param Document $refDocument
     *
     * @return Meeting
     */
    public function setRefDocument(Document $refDocument = null)
    {
        $this->refDocument = $refDocument;

        return $this;
    }

    /**
     * Get refDocument
     *
     * @return Document
     */
    public function getRefDocument()
    {
        return $this->refDocument;
    }
}
