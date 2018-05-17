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

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Client
 *
 * @ORM\Table(name="client", indexes={@ORM\Index(name="idx_client", columns={"user_ref"})})
 * @ORM\Entity
 */
class Client
{
    /**
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="adress", type="text", length=65535, nullable=true)
     */
    private $adress;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="text", nullable=true)
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="text", length=65535, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="text", length=65535, nullable=true)
     */
    private $country;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \BillAndGoBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="BillAndGoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_ref", referencedColumnName="id")
     * })
     */
    private $userRef;

    /**
     * @ORM\ManyToMany(targetEntity="ClientContact", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="client_clientcontact",
     *		joinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id", unique=true)}
     *		)
     */
    private $contactRef;



    /**
     * Set companyName
     *
     * @param string $companyName
     *
     * @return Client
     */
    public function setCompanyName(string $companyName) : self
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName() : string
    {
        return $this->companyName;
    }

    /**
     * Set adress
     *
     * @param string $adress
     *
     * @return Client
     */
    public function setAdress(string $adress) : self
    {
        $this->adress = $adress;
        return $this;
    }

    /**
     * Get adress
     *
     * @return string
     */
    public function getAdress() : string
    {
        return $this->adress;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     *
     * @return Client
     */
    public function setZipcode(string $zipcode) : self
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode() : string
    {
        return $this->zipcode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Client
     */
    public function setCity(string $city) : self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity() : string
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Client
     */
    public function setCountry(string $country) : self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry() : string
    {
        return $this->country;
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
     * Set userRef
     *
     * @param User $userRef
     *
     * @return Client
     */
    public function setUserRef(User $userRef = null) : self
    {
        $this->userRef = $userRef;
        return $this;
    }

    /**
     * Get userRef
     *
     * @return User
     */
    public function getUserRef() : User
    {
        return $this->userRef;
    }
    
    /**
     * @param ClientContact $contact
     *
     * @return Client
     */
    public function addContact(ClientContact $contact) : self
    {
    	$this->contactRef[] = $contact;
    	return $this;
    }
    
    /**
     * @param ClientContact $contact
     *
     * @return Client
     */
    public function removeContact(ClientContact $contact) : self
    {
    	$this->contactRef->removeElement($contact);
    	return $this;
    }

    /**
     * @return Collection(ClientContact)
     */
    public function getContacts() : Collection
    {
    	return $this->contactRef;
    }

    /**
     * Client constructor.
     */
    public function __construct()
    {
    	$this->contactRef = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function stringify() : string
    {
        $data = [
            'id'            => $this->id,
            'companyName'   => $this->companyName,
            'address'       => $this->adress,
            'zipcode'       => $this->zipcode,
            'city'          => $this->city,
            'country'       => $this->country
        ];
        $data["contacts"] = [];
        foreach ($this->getContacts() as $contact) {
            /** @var ClientContact $contact */
            $data["contacts"][$contact->getId()] = [
                'id'        => $contact->getId(),
                'firstname' => $contact->getFirstname(),
                'lastname'  => $contact->getLastname(),
                'email'     => $contact->getEmail(),
                'phone'     => $contact->getPhone(),
                'mobile'    => $contact->getMobile()
            ];
        }
        return json_encode($data);
    }
}
