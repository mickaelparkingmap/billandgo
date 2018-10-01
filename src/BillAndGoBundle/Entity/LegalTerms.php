<?php

namespace BillAndGoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LegalTerms
 *
 * @ORM\Table(name="legal_terms")
 * @ORM\Entity(repositoryClass="BillAndGoBundle\Repository\LegalTermsRepository")
 */
class LegalTerms
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=255)
     */
    private $version;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publicationDate", type="date", nullable=true)
     */
    private $publicationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="date")
     */
    private $creationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;


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
     * Set name
     *
     * @param string $name
     *
     * @return LegalTerms
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
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return LegalTerms
     */
    public function setVersion(string $version) : self
    {
        $this->version = trim(strip_tags($version));
        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion() : ?string
    {
        return $this->version;
    }

    /**
     * Set publicationDate
     *
     * @param \DateTime $publicationDate
     *
     * @return LegalTerms
     */
    public function setPublicationDate(\DateTime $publicationDate) : self
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    /**
     * Get publicationDate
     *
     * @return \DateTime
     */
    public function getPublicationDate() : ?\DateTime
    {
        return $this->publicationDate;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return LegalTerms
     */
    public function setCreationDate(\DateTime $creationDate) : self
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate() : ?\DateTime
    {
        return $this->creationDate;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return LegalTerms
     */
    public function setContent(string $content) : self
    {
        $this->content = trim(strip_tags($content));
        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent() : ?string
    {
        return $this->content;
    }
}
