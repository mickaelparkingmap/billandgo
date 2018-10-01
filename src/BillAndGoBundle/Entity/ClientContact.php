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
 * ClientContact
 *
 * @ORM\Table(name="client_contact")
 * @ORM\Entity
 */
class ClientContact
{
    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=100, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=100, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=12, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=12, nullable=true)
     */
    private $mobile;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return ClientContact
     */
    public function setFirstname(string $firstname) : self
    {
        $this->firstname = trim(strip_tags($firstname));
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname() : ?string
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return ClientContact
     */
    public function setLastname(string $lastname) : self
    {
        $this->lastname = trim(strip_tags($lastname));
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname() : ?string
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ClientContact
     */
    public function setEmail(string $email) : self
    {
        $this->email = trim(strip_tags($email));
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return ClientContact
     */
    public function setPhone(string $phone) : self
    {
        $this->phone = trim(strip_tags($phone));
        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() : ?string
    {
        return $this->phone;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return ClientContact
     */
    public function setMobile(string $mobile) : self
    {
        $this->mobile = trim(strip_tags($mobile));
        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile() : ?string
    {
        return $this->mobile;
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
}
