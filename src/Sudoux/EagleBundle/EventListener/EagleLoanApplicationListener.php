<?php

namespace Sudoux\EagleBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Sudoux\EagleBundle\Entity\LoanApplicationFull;
use Sudoux\MortgageBundle\EventListener\LoanApplicationListener;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EagleLoanApplicationListener
 * @package Sudoux\EagleBundle\EventListener
 * @author Eric Haynes
 */
class EagleLoanApplicationListener extends LoanApplicationListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
       parent::__construct($container);
    }


}