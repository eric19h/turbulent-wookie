<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Sudoux\Cms\UserBundle\Entity\User;
use Sudoux\MortgageBundle\Entity\LoanApplicationRepository;
use Sudoux\MortgageBundle\Entity\LoanOfficer;

/**
 * Class EagleLoanApplicationRepository
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class EagleLoanApplicationRepository extends LoanApplicationRepository
{
}
