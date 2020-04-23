<?php

namespace AppBundle\Traits;

use Symfony\Bridge\Doctrine\RegistryInterface;

trait RegistryTrait
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @return RegistryInterface
     */
    public function getDoctrine(): RegistryInterface
    {
        return $this->doctrine;
    }

    /**
     * @param RegistryInterface $doctrine
     *
     * @return void
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }
}
