<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="tblProductData", indexes={@ORM\Index(name="strProductCode_idx", columns={"strProductCode"})})
 */
class Product
{
    /**
     * @var int
     * @ORM\Column(name="intProductDataId", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Product name should not be blank");
     * @Assert\Type(type="string");
     *
     * @ORM\Column(name="strProductName", type="string", length=50)
     */
    protected $productName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Product desc should not be blank");
     * @Assert\Type(type="string")
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255)
     */
    protected $productDesc;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Product should not be blank")
     * @Assert\Type(type="string")
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, unique=true)
     */
    protected $productCode;

    /**
     * @var \DateTime
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime(message="This property should be a DateTime")
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    protected $dateDiscontinued;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Stock should not be blank")
     * @Assert\Type(type="numeric", message="Stock should be of type integer")
     *
     * @ORM\Column(name="intStock", type="integer")
     */
    protected $stock;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Cost cost should not be blank")
     * @Assert\Type(type="numeric", message="Cost should be of type integer")
     *
     * @ORM\Column(name="fltCost", type="float", options={"unsigned"=true})
     */
    protected $cost;

    /**
     * @var \DateTime
     * @ORM\Column(name="stmTimestamp", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    /**
     * @param $stock
     *
     * @return $this
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return int
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param $cost
     *
     * @return $this
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Get intProductDataId.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set strProductName.
     *
     * @param string $productName
     *
     * @return Product
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get strProductName.
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set strProductDesc.
     *
     * @param string $productDesc
     *
     * @return Product
     */
    public function setProductDesc($productDesc)
    {
        $this->productDesc = $productDesc;

        return $this;
    }

    /**
     * Get strProductDesc.
     *
     * @return string
     */
    public function getProductDesc()
    {
        return $this->productDesc;
    }

    /**
     * Set strProductCode.
     *
     * @param string $productCode
     *
     * @return Product
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * Get strProductCode.
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    /**
     * Get dtmAdded.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setDateDiscontinued(\DateTime $dateDiscontinued)
    {
        $this->dateDiscontinued = $dateDiscontinued;

        return $this;
    }

    /**
     * Get dtmDiscontinued.
     *
     * @return \DateTime
     */
    public function getDateDiscontinued()
    {
        return $this->dateDiscontinued;
    }
}
