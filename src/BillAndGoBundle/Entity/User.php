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
     * @ORM\Column(name="plan", type="text", length=65535, nullable=true)
     */
    private $plan;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="text", length=65535, nullable=true)
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="jobtype", type="string", length=255, nullable=true)
     */
    private $jobtype;

    /**
     * @return string
     */
    public function getJobtype()
    {
        return $this->jobtype;
    }

    /**
     * @param string $jobtype
     */
    public function setJobtype($jobtype)
    {
        $this->jobtype = $jobtype;
    }


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

    /**
     * @return string
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @param string $plan
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;
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



    /**
     * @ORM\Column(name="user_skin", type="string", length=255, nullable=true)
     */
    protected $userSkinPath;

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     *     maxSizeMessage = "The maxmimum allowed file size is 5MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed.")
     */
    protected $user_skin;

    protected $userSkinTemp;

    /**
     * @return mixed
     */
    public function getUserSkinPath()
    {
        return $this->userSkinPath;
    }

    /**
     * @param mixed $userSkinPath
     */
    public function setUserSkinPath($userSkinPath)
    {
        $this->userSkinPath = $userSkinPath;
    }

    /**
     * @return mixed
     */
    public function getUserSkin()
    {
        return $this->user_skin;
    }


    /**
     * @return mixed
     */
    public function getUserSkinTemp()
    {
        return $this->userSkinTemp;
    }

    /**
     * @param mixed $userSkinTemp
     */
    public function setUserSkinTemp($userSkinTemp)
    {
        $this->userSkinTemp = $userSkinTemp;
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
        elseif (null !== $this->user_skin) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->userSkinPath = $filename.'.'.$this->getUserSkin()->guessExtension();
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
        /*if (null === $this->company_logo && null === $this->user_skin) {
            return;
        }*/

       if (null !== $this->company_logo) {
            // check if we have an old image
            if (isset($this->companyLogoTemp)) {
                // delete the old image
                if (file_exists($this->getUploadRootDir("company") . "/" . $this->companyLogoTemp)) {
                    unlink($this->getUploadRootDir("company") . "/" . $this->companyLogoTemp);
                }
                // clear the temp image path
                $this->companyLogoTemp = null;
            }

            $this->getCompanyLogo()->move(
                $this->getUploadRootDir("company"),
                $this->companyLogoPath
            );

            $this->company_logo = null;
        }
        if (null !== $this->user_skin) {
            // check if we have an old image
            if (isset($this->userSkinTemp)) {
                // delete the old image
                if (file_exists($this->getUploadRootDir("user") . "/" . $this->userSkinTemp)) {
                    unlink($this->getUploadRootDir("user") . "/" . $this->userSkinTemp);
                }
                // clear the temp image path
                $this->userSkinTemp = null;
            }
            $this->getUserSkin()->move(
                $this->getUploadRootDir("user"),
                $this->userSkinPath
            );

            // Clean up the file property as you won't need it anymore

            $this->user_skin = null;
        }
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


    /**
     * Sets avatar.
     *
     * @param UploadedFile User skin $user_skin
     */
    public function setUserSkin(UploadedFile $user_skin = null)
    {
        $this->user_skin = $user_skin;
        // check if we have an old image path
        if (isset($this->userSkinPath)) {
            // store the old name to delete after the update
            $this->userSkinTemp = $this->userSkinPath;
            $this->userSkinPath = null;
        } else {
            $this->userSkinPath = 'initial';
        }
    }

    public function getAbsolutePath()
    {

        if (null !== $this->companyLogoPath) {
            return $this->getUploadRootDir("company").'/'.$this->companyLogoPath;
        }
        if (null !== $this->userSkinPath) {
            return $this->getUploadRootDir("user").'/'.$this->userSkinPath;
        }
        return null;
    }

    public function getWebPath()
    {
        if (null !== $this->companyLogoPath) {
            return $this->getUploadDir("company").'/'.$this->companyLogoPath;
        }
        if (null !== $this->userSkinPath) {
            return $this->getUploadDir("user").'/'.$this->userSkinPath;
        }
        return null;
    }

    public function getUploadDir($type)
    {
        if ("company" === $type) {
            return 'uploads/user/company';
        }
        if ("user" === $type) {
            return 'uploads/user/avatar';
        }
        return null;

    }

    protected function getUploadRootDir($type)
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__.'/../../../web/'.$this->getUploadDir($type);
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


