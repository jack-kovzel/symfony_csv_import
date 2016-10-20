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
     *
     * @Assert\NotBlank(message="Product name is blank");
     * @Assert\Type(type="string");
	 *
	 * @ORM\Column(name="strProductName", type="string", length=50)
     */
    protected $strProductName;

    /**
     * @var string
     *
	 * @Assert\NotBlank(message="Product desc is blank");
	 * @Assert\Type(type="string")
	 *
	 * @ORM\Column(name="strProductDesc", type="string", length=255)
     */
    protected $strProductDesc;

    /**
     * @var string
	 *
	 * @Assert\NotBlank(message="Product code is blank")
	 * @Assert\Type(type="string")
	 *
	 * @ORM\Column(name="strProductCode", type="string", length=10, unique=true)
     */
    protected $strProductCode;

    /**
     * @var \DateTime
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    protected $dtmAdded;

    /**
     * @var \DateTime
	 *
	 * @Assert\DateTime(message="This property should be DateTime")
	 *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    protected $dtmDiscontinued;

    /**
     * @var int
	 *
	 * @Assert\NotBlank(message="Property stock is blank")
     * @Assert\Type(type="numeric")
	 *
     * @ORM\Column(name="intStock", type="integer")
     */
    protected $intStock;

    /**
     * @var float
	 *
     * @Assert\NotBlank(message="Property cost is blank")
	 * @Assert\Type(type="numeric")
     *
     * @ORM\Column(name="fltCost", type="float", options={"unsigned"=true})
     */
    protected $fltCost;

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
