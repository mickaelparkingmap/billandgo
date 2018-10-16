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
 * ClientContact is a contact, owned by a Client,
 * representing an individual working here.
 * For example, a client can have different contacts for accounting and project management.
 * If the client is an individual, it will the only contact.
 *
 * @ORM\Table(name="client_contact")
 * @ORM\Entity
 */
class ClientContact
{
    /**
     * The first name of the contact.
     *
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=100, nullable=true)
     */
    private $firstname;

    /**
     * The last name of the contact.
     *
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=100, nullable=true)
     */
    private $lastname;

    /**
     * The email of the contact, to which mails will be sent. Do not spam him !
     *
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * The phone number of the contact.
     *
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=12, nullable=true)
     */
    private $phone;

    /**
     * The mobile phone number of the contact.
     *
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=12, nullable=true)
     */
    private $mobile;

    /**
     * The internal id of the contact
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set firstname after removing tags
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
     * Set lastname after removing tags
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
     * Set email after removing tags
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
     * Set phone after removing tags
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
     * Set mobile after removing tags
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
