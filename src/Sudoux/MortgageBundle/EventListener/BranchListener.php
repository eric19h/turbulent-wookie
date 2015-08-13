<?php

namespace Sudoux\MortgageBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Sudoux\Cms\MessageBundle\Entity\Email;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Sudoux\Cms\UserBundle\Entity\User;
use Sudoux\MortgageBundle\Entity\Branch;
use Sudoux\MortgageBundle\Entity\LoanOfficer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;

/**
 * Class LoanOfficerListener
 * @package Sudoux\MortgageBundle\EventListener
 * @author Dan Alvare
 */
class BranchListener
{
    /**
     * @var ContainerInterface
     */
	protected $container;

	/**
	 * @var Symfony\Component\Routing\Router
	 */
	protected $router;

	/**
	 * @var
	 */
	protected $em;

    /**
     * @param ContainerInterface $container
     */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function postPersist(LifecycleEventArgs $args)
	{
		$this->em = $args->getEntityManager();
		$entity = $args->getEntity();
		if($entity instanceof Branch) {
			$this->geocodeBranch($entity);
		}
	}

	public function postUpdate(LifecycleEventArgs $args)
	{
		$this->em = $args->getEntityManager();
		$entity = $args->getEntity();
		if($entity instanceof Branch) {
			$this->geocodeBranch($entity);
		}
	}

	/**
	 * @param Branch $branch
	 */
	protected function geocodeBranch(Branch $branch)
	{
		try{
			$location = $branch->getLocation();
			if(isset($location)) {
				$latitude = $location->getLatitude();

				if(empty($latitude)) {
					$stateAbbr = '';
					if(isset($state)) {
						$stateAbbr = $state->getAbbreviation();
					}

					$geocoder = $this->container->get('geocoder.address');
					$address = sprintf('%s %s, %s', $location->getAddress1(), $location->getCity(), $stateAbbr);
					$geoAddress = $geocoder->getGeocodedData($address);

					if(count($geoAddress) > 0) {
						$location->setLatitude($geoAddress[0]['latitude']);
						$location->setLongitude($geoAddress[0]['longitude']);
						$this->em->persist($branch);
						$this->em->flush();
					}
				}
			}

		} catch (\Exception $e) {

		}
	}
}