<?php 
namespace Sudoux\MortgageBundle\Command;

use Doctrine\ORM\EntityManager;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\MortgageBundle\Entity\LoanMilestone;

use Sudoux\MortgageBundle\Entity\LoanMilestoneGroup;

use Sudoux\MortgageBundle\DependencyInjection\LosUtil;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sudoux\Cms\SiteBundle\Entity\Site;

/**
 * Class LosCommand
 * @package Sudoux\MortgageBundle\Command
 * @author Dan Alvare
 */
class LosCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
	protected $em;

    /**
     * @var
     */
	protected $twig;

    /**
     * @var
     */
	protected $logger;

    /**
     * @var OutputInterface
     */
	protected $output;

    /**
     * @var LoanApplication
     */
	protected $loan;

    /**
     * @var LosUtil
     */
	protected $loanUtil;
	
    protected function configure()
    {
        $this
            ->setName('sudoux:mortgage:los')
            ->setDescription('LOS Utility')
            ->addArgument('function', InputArgument::REQUIRED)
			->addOption('site_id',
				null,
				InputOption::VALUE_REQUIRED,
				'The site id for processing.'	
			)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	ini_set('memory_limit', -1);
    	set_time_limit(0);
    	
    	$availableFunctions = array(
    		'upsert_milestones'
    	);
    	
    	$function = $input->getArgument('function');
		
    	if(!in_array($function, $availableFunctions)) {
    		throw new \Exception("Function does not exist");
    	}
    	
    	$this->em = $this->getContainer()->get('doctrine')->getEntityManager();
    	$this->logger = $this->getContainer()->get('logger');
		$this->output = $output;
    	
    	$siteId = $input->getOption('site_id');
    	if(!isset($siteId)) {
    		throw new \Exception("The site_id must be set for this command function");
    	}
    	$site = $this->em->getRepository('SudouxCmsSiteBundle:Site')->find($siteId);
    	if(!isset($site)) {
    		throw new \Exception("The site was not found");    				
    	}
    	
		$this->loanUtil = $this->getContainer()->get('sudoux_mortgage.los_util');

    	switch($function) {
    		case 'upsert_milestones':
    			$this->upsertMilestones($site);
    			break;
    	}
    }

    /**
     * 
     * @param Site $site
     */
    protected function upsertMilestones(Site $site)
    {
    	$losMilestoneGroups = $this->loanUtil->getAllMilestones($site);
    	 
    	foreach($losMilestoneGroups['allMilestones'] as $losMilestoneGroup) {
    		$milestoneGroup = $this->em->getRepository('SudouxMortgageBundle:LoanMilestoneGroup')->findOneByLosId($site, $losMilestoneGroup['ID']);
    		 
    		if(!isset($milestoneGroup)) {
    			$milestoneGroup = new LoanMilestoneGroup();
    		}
    		 
    		$milestoneGroup->setName($losMilestoneGroup['name']);
    		$milestoneGroup->setLosId($losMilestoneGroup['ID']);
    		$milestoneGroup->setSite($site);
    		 
    		$i=0;
    		foreach($losMilestoneGroup['milestones'] as $losMilestone) {
    			$milestone = $this->em->getRepository('SudouxMortgageBundle:LoanMilestone')->findOneMilestoneByLosId($site, $losMilestone['ID'], $losMilestoneGroup['ID']);
    	   
    			if(!isset($milestone)) {
    				$milestone = new LoanMilestone();
    				$milestoneGroup->addMilestone($milestone);
    			}
    	   
    			$milestone->setLosId($losMilestone['ID']);
    			$milestone->setName($losMilestone['name']);
    			$milestone->setWeight($i);
    			$milestone->setMilestoneGroup($milestoneGroup);
    			$this->em->persist($milestone);
    			$i++;
    		}
    		 
    		$this->em->persist($milestoneGroup);
    	}
    	 
    	$this->em->flush();
    }
}