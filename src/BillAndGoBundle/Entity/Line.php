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

use BillAndGoBundle\Repository\LineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Line
 *
 * @ORM\Table(name="line")
 * @ORM\Entity(repositoryClass="BillAndGoBundle\Repository\LineRepository")
 */
class Line
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
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refClient", referencedColumnName="id")
     * })
     */
    private $refClient;

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
     * @ORM\Column(name="quantity", type="smallint")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $price;

    /**
     * @var Tax
     *
     * @ORM\ManyToOne(targetEntity="Tax")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="refTax", referencedColumnName="id")
     * })
     */
    private $refTax;

    /**
     * @var float
     *
     * @ORM\Column(name="estimatedTime", type="decimal", precision=4, scale=1, nullable=true)
     */
    private $estimatedTime;

    /**
     * @var float
     *
     * @ORM\Column(name="chronoTime", type="decimal", precision=4, scale=1, nullable=true)
     */
    private $chronoTime;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadLine", type="date", nullable=true)
     */
    private $deadLine;

    /**
     * @var Project
     *
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="refLines")
     * @ORM\JoinTable(name="line_project",
     *		joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refProject;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Document", mappedBy="refLines")
     * @ORM\JoinTable(name="line_estimate",
     *		joinColumns={@ORM\JoinColumn(name="estimate_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refEstimate;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Document", mappedBy="refLinesB")
     * @ORM\JoinTable(name="line_bill",
     *		joinColumns={@ORM\JoinColumn(name="bill_id", referencedColumnName="id")},
     *		inverseJoinColumns={@ORM\JoinColumn(name="line_id", referencedColumnName="id")}
     *		)
     */
    private $refBill;


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
     * @return Line
     */
    public function setName(string $name) : self
    {
        $this->name = trim(strip_tags($name));
        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Line
     */
    public function setDescription(string $description) : self
    {
        $this->description = trim(strip_tags($description));
        return $this;
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Line
     */
    public function setQuantity(int $quantity) : self
    {
        if ($quantity > 0) {
            $this->quantity = $quantity;
        }
        return $this;
    }

    /**
     * Get quantity
     *
     * @return int|null
     */
    public function getQuantity() : ?int
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Line
     */
    public function setPrice(float $price) : self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice() : ?float
    {
        return $this->price;
    }

    /**
     * Set estimatedTime
     *
     * @param float $estimatedTime
     *
     * @return Line
     */
    public function setEstimatedTime(float $estimatedTime) : self
    {
        if ($estimatedTime >= 0) {
            $this->estimatedTime = $estimatedTime;
        }
        return $this;
    }

    /**
     * Get estimatedTime
     *
     * @return float
     */
    public function getEstimatedTime() : ?float
    {
        return $this->estimatedTime;
    }

    /**
     * Set chronoTime
     *
     * @param float $chronoTime
     *
     * @return Line
     */
    public function setChronoTime(float $chronoTime) : self
    {
        if ($chronoTime >= 0) {
            $this->chronoTime = $chronoTime;
        }
        return $this;
    }

    /**
     * Get chronoTime
     *
     * @return float
     */
    public function getChronoTime() : ?float
    {
        return $this->chronoTime;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Line
     */
    public function setStatus(string $status) : self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus() : ?string
    {
        return $this->status;
    }

    /**
     * Set deadLine
     *
     * @param \DateTime $deadLine
     *
     * @return Line
     */
    public function setDeadLine(?\DateTime $deadLine) : self
    {
        $this->deadLine = $deadLine;
        return $this;
    }

    /**
     * Get deadLine
     *
     * @return \DateTime|null
     */
    public function getDeadLine() : ?\DateTime
    {
        return $this->deadLine;
    }

    /**
     * Set refUser
     *
     * @param User $refUser
     *
     * @return Line
     */
    public function setRefUser(?User $refUser = null) : self
    {
        $this->refUser = $refUser;
        return $this;
    }

    /**
     * Get refUser
     *
     * @return User
     */
    public function getRefUser() : ?User
    {
        return $this->refUser;
    }

    /**
     * Set refClient
     *
     * @param Client|null $refClient
     *
     * @return Line
     */
    public function setRefClient(?Client $refClient = null) : self
    {
        $this->refClient = $refClient;
        return $this;
    }

    /**
     * Get refClient
     *
     * @return Client|null
     */
    public function getRefClient() : ?Client
    {
        return $this->refClient;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->refProject = new ArrayCollection();
    }

    /**
     * Add refProject
     *
     * @param Project $refProject
     *
     * @return Line
     */
    public function addRefProject(Project $refProject) : self
    {
        $this->refProject[] = $refProject;
        return $this;
    }

    /**
     * Remove refProject
     *
     * @param Project $refProject
     *
     * @return Line
     */
    public function removeRefProject(Project $refProject) : self
    {
        $this->refProject->removeElement($refProject);
        return $this;
    }

    /**
     * Get refProject
     *
     * @return Collection
     */
    public function getRefProject() : Collection
    {
        return $this->refProject;
    }

    /**
     * Add refEstimate
     *
     * @param Document $refEstimate
     *
     * @return Line
     */
    public function addRefEstimate(Document $refEstimate) : self
    {
        $this->refEstimate->add($refEstimate);
        return $this;
    }

    /**
     * Remove refEstimate
     *
     * @param Document $refEstimate
     *
     * @return Line
     */
    public function removeRefEstimate(Document $refEstimate) : self
    {
        $this->refEstimate->removeElement($refEstimate);
        return $this;
    }

    /**
     * Get refEstimate
     *
     * @return Collection
     */
    public function getRefEstimate() : Collection
    {
        return $this->refEstimate;
    }

    /**
     * Add refBill
     *
     * @param Document $refBill
     *
     * @return Line
     */
    public function addRefBill(Document $refBill) : self
    {
        $this->refBill->add($refBill);
        return $this;
    }

    /**
     * Remove refBill
     *
     * @param Document $refBill
     *
     * @return Line
     */
    public function removeRefBill(Document $refBill) : self
    {
        $this->refBill->removeElement($refBill);
        return $this;
    }

    /**
     * Get refBill
     *
     * @return Collection
     */
    public function getRefBill() : Collection
    {
        return $this->refBill;
    }

    /**
     * Set refTax
     *
     * @param Tax|null $refTax
     *
     * @return Line
     */
    public function setRefTax(?Tax $refTax = null) : self
    {
        $this->refTax = $refTax;
        return $this;
    }

    /**
     * Get refTax
     *
     * @return Tax|null
     */
    public function getRefTax() : ?Tax
    {
        return $this->refTax;
    }
}
