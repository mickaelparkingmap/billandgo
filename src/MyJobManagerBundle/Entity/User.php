<?php
// src/AppBundle/Entity/User.php

namespace MyJobManagerBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

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
     * @ORM\Column(name="companyname", type="string", length=255, nullable=false)
     */
    private $companyname;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", nullable=true)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="siret", type="text", length=65535, nullable=true)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="banque", type="text", length=65535, nullable=true)
     */
    private $banque;

    /**
     * @var string
     *
     * @ORM\Column(name="bic", type="text", length=65535, nullable=true)
     */
    private $bic;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="text", length=65535, nullable=true)
     */
    private $iban;




    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set companyname
     *
     * @param string $companyname
     *
     * @return User
     */
    public function setCompanyname($companyname)
    {
        $this->companyname = $companyname;

        return $this;
    }

    /**
     * Get companyname
     *
     * @return string
     */
    public function getCompanyname()
    {
        return $this->companyname;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set zipCode
     *
     * @param integer $zipCode
     *
     * @return User
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return integer
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set mobile
     *
     * @param integer $mobile
     *
     * @return User
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return integer
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set phone
     *
     * @param integer $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return integer
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set siret
     *
     * @param string $siret
     *
     * @return User
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set banque
     *
     * @param string $banque
     *
     * @return User
     */
    public function setBanque($banque)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return string
     */
    public function getBanque()
    {
        return $this->banque;
    }

    /**
     * Set bic
     *
     * @param string $bic
     *
     * @return User
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }
    

    /**
     * Get bic
     *
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Set iban
     *
     * @param string $iban
     *
     * @return User
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**** *****/



    /**
     * @ORM\Column(name="company_logo", type="string", length=255, nullable=true)
     */
    protected $companyLogoPath;

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     *     maxSizeMessage = "The maxmimum allowed file size is 5MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed.")
     */
    protected $company_logo;

    protected $companyLogoTemp;


    public function getCompanyLogo()
    {
        return ($this->company_logo);
    }

    /**
     * @return mixed
     */
    public function getCompanyLogoPath()
    {
        return $this->companyLogoPath;
    }

    /**
     * @param mixed $companyLogoPath
     */
    public function setCompanyLogoPath($companyLogoPath)
    {
        $this->companyLogoPath = $companyLogoPath;
    }

    /**
     * @return mixed
     */
    public function getCompanyLogoTemp()
    {
        return $this->companyLogoTemp;
    }

    /**
     * @param mixed $companyLogoTemp
     */
    public function setCompanyLogoTemp($companyLogoTemp)
    {
        $this->companyLogoTemp = $companyLogoTemp;
    }





    /*
    ******************** Profile Avatar upload ********************
    */

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->company_logo) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->companyLogoPath = $filename.'.'.$this->getCompanyLogo()->guessExtension();
        }
    }

    /**
     * Called after entity persistence
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // The file property can be empty if the field is not required
        if (null === $this->company_logo) {
            return;
        }

        // check if we have an old image
        if (isset($this->companyLogoTemp)) {
            // delete the old image
            unlink($this->getUploadRootDir()."/".$this->companyLogoTemp);
            // clear the temp image path
            $this->companyLogoTemp = null;
        }

        $this->getCompanyLogo()->move(
            $this->getUploadRootDir(),
            $this->companyLogoPath
        );

        // Clean up the file property as you won't need it anymore
        $this->company_logo = null;
    }

    /**
     * Sets avatar.
     *
     * @param Uploaded Company Logo $company_logo
     */
    public function setCompanyLogo(UploadedFile $company_logo = null)
    {
        $this->company_logo = $company_logo;
        // check if we have an old image path
        if (isset($this->companyLogoPath)) {
            // store the old name to delete after the update
            $this->companyLogoTemp = $this->companyLogoPath;
            $this->companyLogoPath = null;
        } else {
            $this->companyLogoPath = 'initial';
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->companyLogoPath
            ? null
            : $this->getUploadRootDir().'/'.$this->companyLogoPath;
    }

    public function getWebPath()
    {
        return null === $this->companyLogoPath
            ? null
            : $this->getUploadDir().'/'.$this->companyLogoPath;
    }

    public function getUploadDir()
    {
        return 'uploads/user/avatar';
    }

    protected function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    public function __toString()
    {
        return $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }


    public function getFull()
    {
        return $this->lastname." ".$this->firstname;
    }




}


