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
 * Tax
 *
 * @ORM\Table(name="tax")
 * @ORM\Entity(repositoryClass="BillAndGoBundle\Repository\TaxRepository")
 */
class Tax
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="percent", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $percent;

    /**
     * @var string
     *
     * @ORM\Column(name="help", type="text", nullable=true)
     */
    private $help;


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
     * @return Tax
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
     * Set percent
     *
     * @param integer $percent
     *
     * @return Tax
     */
    public function setPercent(int $percent) : self
    {
        $this->percent = $percent;
        return $this;
    }

    /**
     * Get percent
     *
     * @return int
     */
    public function getPercent() : int
    {
        return $this->percent;
    }

    /**
     * Set help
     *
     * @param string $help
     *
     * @return Tax
     */
    public function setHelp(string $help) : self
    {
        $this->help = trim(strip_tags($help));
        return $this;
    }

    /**
     * Get help
     *
     * @return string
     */
    public function getHelp() : string
    {
        return $this->help;
    }
}
