<?php

namespace Sudoux\MortgageBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoanApplicationListener
 * @package Sudoux\MortgageBundle\EventListener
 * @author Dan Alvare
 */
class LoanApplicationListener
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
        $this->container = $container;
    }

    /**
     * The onFlush method will reassign loans to loan officer sites if they have one
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof LoanApplication) {

                // reassign the loan application to the LO site
                $lo = $entity->getLoanOfficer();
                //echo 'here 1';
                if (isset($lo)) {
                    $hasSite = $lo->hasSite();
                    if ($hasSite) {
                        // check if they are different
                        //print_r('Loan ID: ' . $entity->getId() . '---'. $lo->getOfficerSite()->getId() . ':' . $entity->getSite()->getId() . ' ');
                        $additionalSiteIds = array();
                        $additionalSites = $entity->getAdditionalSite();
                        if(isset($additionalSites)) {
                            foreach ($entity->getAdditionalSite() as $site) {
                                array_push($additionalSiteIds, $site->getId());
                            }
                        }

                        //print_r($additionalSiteIds);

                        if ($lo->getOfficerSite()->getId() != $entity->getSite()->getId() && !in_array($lo->getOfficerSite()->getId(), $additionalSiteIds)) {
                            $entity->addAdditionalSite($lo->getOfficerSite());
                            $uow->persist($entity);
                            //print_r('add site');
                        }
                    }
                }
            }
        }

        $uow->computeChangeSets();
    }
}