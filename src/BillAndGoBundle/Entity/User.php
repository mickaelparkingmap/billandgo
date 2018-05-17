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
    public function getJobtype () : ?string
    {
        return $this->jobtype;
    }

    /**
     * @param string $jobtype
     * @return User
     */
    public function setJobtype (string $jobtype) : self
    {
        $this->jobtype = $jobtype;
        return $this;
    }


    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname (string $firstname) : self
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname () : ?string
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
    public function setLastname (string $lastname) : self
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname () : ?string
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
    public function setCompanyname (string $companyname) : self
    {
        $this->companyname = $companyname;
        return $this;
    }

    /**
     * Get companyname
     *
     * @return string
     */
    public function getCompanyname () : ?string
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
    public function setAddress (string $address) : self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress () : ?string
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
    public function setZipCode (string $zipCode) : self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * Get zipCode
     *
     * @return integer
     */
    public function getZipCode () : ?string
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
    public function setCity (string $city) : self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity () : ?string
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
    public function setCountry (string $country) : self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry () : ?string
    {
        return $this->country;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return User
     */
    public function setMobile (string $mobile) : self
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile () : ?string
    {
        return $this->mobile;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone (string $phone) : self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone () : ?string
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
    public function setSiret (string $siret) : self
    {
        $this->siret = $siret;
        return $this;
    }

    /**
     * Get siret
     *
     * @return string
     */
    public function getSiret () : ?string
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
    public function setBanque (string $banque) : self
    {
        $this->banque = $banque;
        return $this;
    }

    /**
     * Get banque
     *
     * @return string
     */
    public function getBanque () : ?string
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
    public function setBic (string $bic) : self
    {
        $this->bic = $bic;
        return $this;
    }
    

    /**
     * Get bic
     *
     * @return string
     */
    public function getBic () : ?string
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
    public function setIban (string $iban) : self
    {
        $this->iban = $iban;
        return $this;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban () : ?string
    {
        return $this->iban;
    }

    /**
     * @return string
     */
    public function getPlan () : ?string
    {
        return $this->plan;
    }

    /**
     * @param string $plan
     */
    public function setPlan (string $plan)
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
     * @return string|null
     */
    public function getCompanyLogoPath () : ?string
    {
        return $this->companyLogoPath;
    }

    /**
     * @param string $companyLogoPath
     * @return User
     */
    public function setCompanyLogoPath (string $companyLogoPath) : self
    {
        $this->companyLogoPath = $companyLogoPath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyLogoTemp ()
    {
        return $this->companyLogoTemp;
    }

    /**
     * @param mixed $companyLogoTemp
     * @return User
     */
    public function setCompanyLogoTemp ($companyLogoTemp) : self
    {
        $this->companyLogoTemp = $companyLogoTemp;
        return $this;
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
     * @return string
     */
    public function getUserSkinPath () : ?string
    {
        return $this->userSkinPath;
    }

    /**
     * @param string $userSkinPath
     * @return User
     */
    public function setUserSkinPath (string $userSkinPath) : self
    {
        $this->userSkinPath = $userSkinPath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserSkin ()
    {
        return $this->user_skin;
    }


    /**
     * @return mixed
     */
    public function getUserSkinTemp ()
    {
        return $this->userSkinTemp;
    }

    /**
     * @param mixed $userSkinTemp
     * @return User
     */
    public function setUserSkinTemp($userSkinTemp) : self
    {
        $this->userSkinTemp = $userSkinTemp;
        return $this;
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
     * @param UploadedFile Company Logo $company_logo
     * @return User
     */
    public function setCompanyLogo (UploadedFile $company_logo = null) : self
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
        return $this;
    }


    /**
     * Sets avatar.
     *
     * @param UploadedFile User skin $user_skin
     * @return User
     */
    public function setUserSkin (UploadedFile $user_skin = null) : self
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
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAbsolutePath () : ?string
    {

        if (null !== $this->companyLogoPath) {
            return $this->getUploadRootDir("company").'/'.$this->companyLogoPath;
        }
        if (null !== $this->userSkinPath) {
            return $this->getUploadRootDir("user").'/'.$this->userSkinPath;
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getWebPath () : ?string
    {
        if (null !== $this->companyLogoPath) {
            return $this->getUploadDir("company").'/'.$this->companyLogoPath;
        }
        if (null !== $this->userSkinPath) {
            return $this->getUploadDir("user").'/'.$this->userSkinPath;
        }
        return null;
    }

    /**
     * @param string $type
     * @return null|string
     */
    public function getUploadDir (string $type) : ?string
    {
        if ("company" === $type) {
            return 'uploads/user/company';
        }
        if ("user" === $type) {
            return 'uploads/user/avatar';
        }
        return null;

    }

    /**
     * @param string $type
     * @return string
     */
    protected function getUploadRootDir (string $type) : string
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__.'/../../../web/'.$this->getUploadDir($type);
    }

    /**
     * @return string
     */
    public function __toString ()
    {
        return $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload ()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }

    /**
     * @return string
     */
    public function getFull () : string
    {
        return $this->lastname." ".$this->firstname;
    }

    public function stringify () : string
    {
        $data = [
            'id'            => $this->id,
            'username'      => $this->username,
            'firstname'     => $this->firstname,
            'lastname'      => $this->lastname,
            'companyname'   => $this->companyname,
            'SIRET'         => $this->siret,
            'jobtype'       => $this->jobtype,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'mobile'        => $this->mobile,
            'enabled'       => $this->enabled,
            'plan'          => $this->plan,
        ];
        $data['location'] = [
            'address'       => $this->address,
            'zipcode'       => $this->zipCode,
            'city'          => $this->city,
            'country'       => $this->country
        ];
        $data['bank'] = [
            'name'          => $this->banque,
            'bic'           => $this->bic,
            'iban'          => $this->iban,
        ];
        return json_encode($data);
    }




}


