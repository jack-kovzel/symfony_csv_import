<?php

/**
 * Product entity
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Product
 * @package CsvBundle\Entity
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
    protected $intProductDataId;

    /**
     * @var string
     * @ORM\Column(name="strProductName", type="string", length=50)
     * @Assert\NotBlank(message="Product name is blank");
     */
    protected $strProductName;

    /**
     * @var string
     * @ORM\Column(name="strProductDesc", type="string", length=255)
     * @Assert\NotBlank(message="Product desc is blank");
     */
    protected $strProductDesc;

    /**
     * @var string
     * @ORM\Column(name="strProductCode", type="string", length=10, unique=true)
     * @Assert\NotBlank(message="Product code is blank")
     */
    protected $strProductCode;

    /**
     * @var \DateTime
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    protected $dtmAdded;

    /**
     * @var \DateTime
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    protected $dtmDiscontinued;

    /**
     * @var int
     * @Assert\Type(type="numeric", message="Property stock should be of type numeric")
     * @Assert\NotBlank(message="Property stock is blank")
     * @ORM\Column(name="intStock", type="integer")
     */
    protected $intStock;

    /**
     * @var float
     * @Assert\Type(type="numeric", message="Property cost should be of type numeric")
     * @Assert\NotBlank(message="Property cost is blank")
     * @ORM\Column(name="fltCost", type="float", options={"unsigned"=true})
     */
    protected $fltCost;

    /**
     * @var string|null
     */
    protected $discontinued;

    /**
     * @var \DateTime
     * @ORM\Column(name="stmTimestamp", type="datetime")
     */
    protected $stmTimestamp;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setStmTimestamp()
    {
        $this->stmTimestamp = new \DateTime();
    }

    /**
     * @param $intStock
     * @return $this
     */
    public function setIntStock($intStock)
    {
        return  $this->intStock = $intStock;
    }

    /**
     * @return int
     */
    public function getIntStock()
    {
        return $this->intStock;
    }

    /**
     * @param $fltCost
     * @return $this
     */
    public function setFltCost($fltCost)
    {
        return $this->fltCost = $fltCost;
    }

    /**
     * @return float
     */
    public function getFltCost()
    {
        return $this->fltCost;
    }

    /**
     * @param $discontinued
     * @return $this
     */
    public function setDiscontinued($discontinued)
    {
        $this->discontinued = $discontinued;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDiscontinued()
    {
        return $this->discontinued;
    }

    /**
     * Get intProductDataId
     *
     * @return integer
     */
    public function getIntProductDataId()
    {
        return $this->intProductDataId;
    }

    /**
     * Set strProductName
     *
     * @param string $strProductName
     *
     * @return Product
     */
    public function setStrProductName($strProductName)
    {
        $this->strProductName = $strProductName;

        return $this;
    }

    /**
     * Get strProductName
     *
     * @return string
     */
    public function getStrProductName()
    {
        return $this->strProductName;
    }

    /**
     * Set strProductDesc
     *
     * @param string $strProductDesc
     *
     * @return Product
     */
    public function setStrProductDesc($strProductDesc)
    {
        $this->strProductDesc = $strProductDesc;

        return $this;
    }

    /**
     * Get strProductDesc
     *
     * @return string
     */
    public function getStrProductDesc()
    {
        return $this->strProductDesc;
    }

    /**
     * Set strProductCode
     *
     * @param string $strProductCode
     *
     * @return Product
     */
    public function setStrProductCode($strProductCode)
    {
        $this->strProductCode = $strProductCode;

        return $this;
    }

    /**
     * Get strProductCode
     *
     * @return string
     */
    public function getStrProductCode()
    {
        return $this->strProductCode;
    }

    /**
     * @ORM\PrePersist
     */
    public function setDtmAdded()
    {
        $this->dtmAdded = new \DateTime();
    }

    /**
     * Get dtmAdded
     *
     * @return \DateTime
     */
    public function getDtmAdded()
    {
        return $this->dtmAdded;
    }

    public function setDtmDiscontinued(\DateTime $dtmDiscontinued)
    {
		$this->dtmDiscontinued = $dtmDiscontinued;
    }

    /**
     * Get dtmDiscontinued
     *
     * @return \DateTime
     */
    public function getDtmDiscontinued()
    {
        return $this->dtmDiscontinued;
    }
}
