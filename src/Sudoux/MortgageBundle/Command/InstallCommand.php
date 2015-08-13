<?php

namespace Sudoux\MortgageBundle\Command;

use Sudoux\Cms\SiteBundle\Entity\InstallationProfile;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Sudoux\Cms\SiteBundle\Entity\SiteType;
use Sudoux\Cms\UserBundle\Entity\Role;

use Sudoux\MortgageBundle\Entity\CreditProvider;
use Sudoux\MortgageBundle\Entity\LosProvider;
use Sudoux\MortgageBundle\Entity\PricingProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sudoux\Cms\SiteBundle\Command\InstallCommand as CmsInstallCommand;
use Doctrine\ORM\EntityManager;

/**
 * Class InstallCommand
 * @package Sudoux\MortgageBundle\Command
 */
class InstallCommand extends CmsInstallCommand
{
    protected function configure()
    {
        $this
            ->setName('sudoux:mortgage:install')
            ->setDescription('Installs mortgage components')
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'The site name'
            )
            ->addOption(
                'domain',
                null,
                InputOption::VALUE_REQUIRED,
                'The domain (ex. mydomain.localhost)'
            )
            ->addOption(
                'email',
                null,
                InputOption::VALUE_REQUIRED,
                'The website email address'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $loRole = $em->getRepository('SudouxCmsUserBundle:Role')->findOneBy(array('role' => 'ROLE_LOAN_OFFICER'));
        if(isset($loRole)) {
            throw new \Exception('MortgageWare is already installed.');
        }

        $associateRole = new Role();
        $associateRole->setName('Loan Officer');
        $associateRole->setRole('ROLE_LOAN_OFFICER');
        $associateRole->setPublic(true);
        $em->persist($associateRole);

        $installationProfile = new InstallationProfile();
        $installationProfile->setName('Default Profile');
        $installationProfile->setClassName('\Sudoux\MortgageBundle\Profile\DefaultProfile');
        $em->persist($installationProfile);

        $siteType = new SiteType();
        $siteType->setName('Branch');
        $siteType->setKeyName('branch');
        $siteType->setSelectable(true);
        $em->persist($siteType);

        $siteType = new SiteType();
        $siteType->setName('Loan Officer');
        $siteType->setKeyName('loan_officer');
        $siteType->setSelectable(true);
        $em->persist($siteType);

        // add the integrations
        $losProviders = array('\Sudoux\MortgageBundle\Model\Los\Encompass' => 'Encompass', '\Sudoux\MortgageBundle\Model\Los\MortgageBuilder' => 'Mortgage Builder');
        foreach($losProviders as $key => $value) {
            $los = new LosProvider();
            $los->setClassName($key);
            $los->setName($value);
            $em->persist($los);
        }

        $pricingProviders = array('\Sudoux\MortgageBundle\Model\Pricing\DemoIntegration' => 'Demo Integration');
        foreach($pricingProviders as $key => $value) {
            $pricing = new PricingProvider();
            $pricing->setName($value);
            $pricing->setClassName($key);
            $em->persist($pricing);
        }

        $creditProviders = array('\Sudoux\MortgageBundle\Model\Credit\DemoIntegration' => 'Demo Integration');
        foreach($creditProviders as $key => $value) {
            $credit = new CreditProvider();
            $credit->setName($value);
            $credit->setClassName($key);
            $em->persist($credit);
        }

        $em->flush();
    }

    protected function installSiteProfile(EntityManager $em, Site $site)
    {
        // run the profile installation
        $profile = new \Sudoux\MortgageBundle\Profile\DefaultProfile($em, $site, false);
        $profile->execute();
    }
}