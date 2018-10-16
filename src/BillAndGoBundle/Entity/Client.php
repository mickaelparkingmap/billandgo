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

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Client represents a client or prospect to which estimates, projects and bills will be linked.
 * It can be a company, with one or several contacts, or a particular wxith a single contact, himself.
 *
 * @ORM\Table(name="client", indexes={@ORM\Index(name="idx_client", columns={"user_ref"})})
 * @ORM\Entity(repositoryClass="BillAndGoBundle\Repository\ClientRepository")
 */
class Client
{
    /**
     * The name of the company. If an individual, it will be composed by its firstname and lastname.
     *
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
     */
    private $companyName;

    /**
     * The postal adress of the company, minus the zip code and the city.
     * It is used by billing.
     * Although it is nullable, it must be filled, even for an individual.
     *
     * @var string
     *
     * @ORM\Column(name="adress", type="text", length=65535, nullable=true)
     */
    private $adress;

    /**
     * The zipcode of the postal adress of the company.
     *
     * @var string
     *
     * @ORM\Column(name="zipcode", type="text", nullable=true)
     */
    private $zipcode;

    /**
     * The city of the postal adress of the company.
     *
     * @var string
     *
     * @ORM\Column(name="city", type="text", length=65535, nullable=true)
     */
    private $city;

    /**
     * The country of the company.
     * If both the user and the company are located in France, the field is not mandatory.
     *
     * @var string
     *
     * @ORM\Column(name="country", type="text", length=65535, nullable=true)
     */
    private $country;

    /**
     * The internal id.
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * The user who created this client.
     * Only this user will be able to see it, use it and edit it.
     *
     * @var \BillAndGoBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="BillAndGoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_ref", referencedColumnName="id")
     * })
     */
    private $userRef;

    /**
     * The collection of contacts in this company.
     * At least one must be created to be able to send estimates or bills.
     *
     * @ORM\ManyToMany(targetEntity="ClientContact", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="client_clientcontact",
     *      joinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $contactRef;



    /**
     * Set companyName after removing tags.
     *
     * @param string $companyName
     *
     * @return Client
     */
    public function setCompanyName(string $companyName) : self
    {
        $this->companyName = trim(strip_tags($companyName));
        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName() : ?string
    {
        return $this->companyName;
    }

    /**
     * Set adress after removing tags.
     *
     * @param string $adress
     *
     * @return Client
     */
    public function setAdress(string $adress) : self
    {
        $this->adress = trim(strip_tags($adress));
        return $this;
    }

    /**
     * Get adress
     *
     * @return string
     */
    public function getAdress() : ?string
    {
        return $this->adress;
    }

    /**
     * Set zipcode after removing tags.
     *
     * @param string $zipcode
     *
     * @return Client
     */
    public function setZipcode(string $zipcode) : self
    {
        $this->zipcode = trim(strip_tags($zipcode));
        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode() : ?string
    {
        return $this->zipcode;
    }

    /**
     * Set city after removing tags.
     *
     * @param string $city
     *
     * @return Client
     */
    public function setCity(string $city) : self
    {
        $this->city = trim(strip_tags($city));
        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity() : ?string
    {
        return $this->city;
    }

    /**
     * Set country after removing tags.
     *
     * @param string $country
     *
     * @return Client
     */
    public function setCountry(string $country) : self
    {
        $this->country = trim(strip_tags($country));
        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry() : ?string
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
     * Add a contact to the client.
     *
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
     * Remove a specified contact from the client, it it is found.
     *
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
     * Returns collection of contacts.
     *
     * @return Collection(ClientContact)
     */
    public function getContacts() : Collection
    {
        return $this->contactRef;
    }

    /**
     * Client constructor.
     * Sets the contacts as an empty ArrayCollection.
     */
    public function __construct()
    {
        $this->contactRef = new ArrayCollection();
    }

    /**
     * Returns json containing data of the client and its contacts.
     *
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
