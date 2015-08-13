<?php 

namespace Sudoux\MortgageBundle\Profile;

use Sudoux\Cms\SiteBundle\Profile\DefaultProfile as SiteDefaultProfile;
use Sudoux\Cms\SiteBundle\Profile\IProfile;
use Sudoux\Cms\TaxonomyBundle\Entity\Vocabulary;

use Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy;

use Doctrine\ORM\EntityManager;

use Sudoux\Cms\NodeBundle\Entity\Page;

use Sudoux\Cms\MenuBundle\Entity\MenuItem;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\Cms\MenuBundle\Entity\Menu;

/**
 * Class DefaultProfile
 * @package Sudoux\MortgageBundle\Profile
 * @author Dan Alvare
 */
class DefaultProfile extends SiteDefaultProfile implements IProfile
{
    /**
     * @param EntityManager $em
     * @param Site $site
     * @param bool $inheritSettings
     */
	public function __construct(EntityManager $em, Site $site, $inheritSettings = true)
	{		
		parent::__construct($em, $site, $inheritSettings);
	}

    /**
     *
     */
	public function execute()
	{
		parent::execute();
	}

    /**
     *
     */
	public function generateMenu() 
	{
		if(!$this->inheritSettings) {
			$primaryMenu = new Menu();
			$primaryMenu->setName('Primary Menu');
			$primaryMenu->setDescription('The main menu');
			$primaryMenu->setSite($this->site);
			$this->em->persist($primaryMenu);
			
			$this->settings->setPrimaryMenu($primaryMenu);
			
			$this->settings->setFooterMenu($primaryMenu);
			$this->settings->setWebsiteEmail($this->user->getEmail());
			
	        // generate menu and dummy nodes
	        $homePage = new Page();
	        $homePage->setTitle('Home');
	        $homePage->setBody('Coming Soon');
	        $homePage->setSite($this->site);
	        $homePage->setUser($this->user);
            $homePage->setAlias('dummy');
	        $homePage->setSlug('home-' . $this->site->getId());
	        $this->em->persist($homePage);
	        
	        $this->settings->setHomepageNode($homePage);
	
	        // top level menu items
	        $homeMenuItem = new MenuItem();
	        $homeMenuItem->setName('Home');
	        $homeMenuItem->setWeight(1);
	        $homeMenuItem->setNode($homePage);
	        $homeMenuItem->setMenu($primaryMenu);
	        $this->em->persist($homeMenuItem);
	        
	        $membersMenuItem = new MenuItem();
	        $membersMenuItem->setName('For Members');
	        $membersMenuItem->setPath('user/account');
	        $membersMenuItem->setWeight(6);
	        $membersMenuItem->setMenu($primaryMenu);
	        $this->em->persist($membersMenuItem);
	        
	            $registerMenuItem = new MenuItem();
	            $registerMenuItem->setName('Register');
	            $registerMenuItem->setPath('register');
	            $registerMenuItem->setParentMenuItem($membersMenuItem);
	            $registerMenuItem->setWeight(1);
	            $registerMenuItem->setMenu($primaryMenu);
	            $this->em->persist($registerMenuItem);
	    
	            $signinMenuItem = new MenuItem();
	            $signinMenuItem->setName('Sign In');
	            $signinMenuItem->setPath('login');
	            $signinMenuItem->setParentMenuItem($membersMenuItem);
	            $signinMenuItem->setWeight(2);
	            $signinMenuItem->setMenu($primaryMenu);
	            $this->em->persist($signinMenuItem);
	
	        $prequalMenuItem = new MenuItem();
	        $prequalMenuItem->setName('Prequalify');
	        $prequalMenuItem->setPath('loan/prequalify');
	        $prequalMenuItem->setWeight(7);
	        $prequalMenuItem->setMenu($primaryMenu);
	        $this->em->persist($prequalMenuItem);
	
	        $applyMenuItem = new MenuItem();
	        $applyMenuItem->setName('Apply Now');
	        $applyMenuItem->setPath('loan/apply/1');
	        $applyMenuItem->setWeight(8);
	        $applyMenuItem->setMenu($primaryMenu);
	        $this->em->persist($applyMenuItem);
	
	        $contactMenuItem = new MenuItem();
	        $contactMenuItem->setName('Contact');
	        $contactMenuItem->setPath('contact');
	        $contactMenuItem->setWeight(9);
	        $contactMenuItem->setMenu($primaryMenu);
	        $this->em->persist($contactMenuItem);
		}
		
        $this->em->flush();
	}

    /**
     *
     */
	public function generateTaxonomies()
	{
		parent::generateTaxonomies();
		
		$loanVocab = new Vocabulary();
		$loanVocab->setName('Loan Document Categories');
		$loanVocab->setSite($this->site);
		$loanVocab->setUser($this->user);
		$loanVocab->setActive(true);
		$loanVocab->setPublic(false);
		$this->em->persist($loanVocab);
		$this->em->flush($loanVocab);
		$this->settings->setLoanDocumentVocab($loanVocab);
	}
}

?>