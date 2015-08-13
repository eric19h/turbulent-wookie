<?php 
namespace Sudoux\MortgageBundle\Twig;

use Doctrine\ORM\EntityManager;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NotificationExtension
 * @package Sudoux\MortgageBundle\Twig
 * @author Dan Alvare
 */
class NotificationExtension extends \Twig_Extension
{
    /**
     * @var Site
     */
	protected $site;

    /**
     * @var object
     */
	protected $siteRequest;

    /**
     * @var EntityManager
     */
	protected $em;

    /**
     * @var ContainerInterface
     */
	protected $container;

    /**
     * @param ContainerInterface $container
     * @param EntityManager $em
     */
	public function __construct(ContainerInterface $container, EntityManager $em)
	{
		if ($container->isScopeActive('request')) {
			$this->em = $em;
			$this->siteRequest = $container->get('sudoux.cms.site');
			$this->site = $this->siteRequest->getSite();
			$this->container = $container;
		}
	}
	
	public function getGlobals() {
		if (isset($this->site)) {
			$siteIds = $this->site->getChildSiteIds();
			array_push($siteIds, $this->site->getId());
			
			$connection = $this->container->get('doctrine')->getConnection();
			$sql = "SELECT s.name, t.name AS type, d.domain, (SELECT COUNT(l.id) FROM lead l WHERE l.lead_status_id = 1 AND l.site_id=s.id) AS leads, ";
			$sql .= "(SELECT COUNT(a.id) FROM loan_application a WHERE a.status <= 1 AND a.deleted = 0 AND a.site_id=s.id) AS loans FROM site s ";
			$sql .= "INNER JOIN domain d ON s.primary_domain_id=d.id ";
			$sql .= "LEFT JOIN site_type t ON s.site_type_id=t.id ";
			$sql .= sprintf("WHERE s.id IN (%s) ", implode(',', $siteIds));
			$sql .= "GROUP BY s.id";
			$stmt = $connection->prepare($sql);
			//echo $sql; exit;
			$stmt->execute();
			$siteSummary = $stmt->fetchAll();	
			
			// get loans
			$qb = $this->em->createQueryBuilder()
					->select('count(l.id)')
					->from('SudouxMortgageBundle:LoanApplication', 'l')
					->where('l.status = 0 OR l.status = 1')
					->andWhere('l.site IN (:site_ids)')
					->andWhere('l.deleted = 0')
					->setParameter('site_ids', $siteIds);
			
			$newLoans = $qb->getQuery()->getSingleScalarResult();
			// get docs
			
			return array(
				'newLoans' => $newLoans,
				'siteSummary' => $siteSummary,
			);
		} else {
			return array();
		}
	}
	
	public function getName()
	{
		return 'notification_extension';
	}
}

?>