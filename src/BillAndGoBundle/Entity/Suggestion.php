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
 * Suggestion
 *
 * @ORM\Table(name="suggestion")
 * @ORM\Entity(repositoryClass="BillAndGoBundle\Repository\SuggestionRepository")
 */
class Suggestion
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="priceHT", type="float", nullable=true)
     */
    private $priceHT;

    /**
     * @var float
     *
     * @ORM\Column(name="time", type="float", nullable=true)
     */
    private $time;


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
     * @return User
     */
    public function getRefUser(): User
    {
        return $this->refUser;
    }

    /**
     * @param User $refUser
     * @return Suggestion
     */
    public function setRefUser(User $refUser): Suggestion
    {
        $this->refUser = $refUser;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Suggestion
     */
    public function setName(string $name) : self
    {
        $this->name = $name;
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
     * @return Suggestion
     */
    public function setDescription(string $description) : self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * Set priceHT
     *
     * @param float $priceHT
     *
     * @return Suggestion
     */
    public function setPriceHT(float $priceHT) : self
    {
        $this->priceHT = $priceHT;
        return $this;
    }

    /**
     * Get priceHT
     *
     * @return float
     */
    public function getPriceHT() : float
    {
        return $this->priceHT;
    }

    /**
     * Set time
     *
     * @param float $time
     *
     * @return Suggestion
     */
    public function setTime($time) : self
    {
        $this->time = $time;
        return $this;
    }

    /**
     * Get time
     *
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }
}
