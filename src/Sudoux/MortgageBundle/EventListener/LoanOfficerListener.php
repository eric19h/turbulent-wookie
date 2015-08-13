<?php

namespace Sudoux\MortgageBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Sudoux\Cms\MessageBundle\Entity\Email;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Sudoux\Cms\UserBundle\Entity\User;
use Sudoux\MortgageBundle\Entity\LoanOfficer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;

/**
 * Class LoanOfficerListener
 * @package Sudoux\MortgageBundle\EventListener
 * @author Dan Alvare
 */
class LoanOfficerListener
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
	public function __construct(ContainerInterface $container, Router $router)
	{
		$this->container = $container;
		$this->router = $router;
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function postPersist(LifecycleEventArgs $args)
	{
		$this->em = $args->getEntityManager();
		$entity = $args->getEntity();
		if ($entity instanceof LoanOfficer) {
			$this->addLoanOfficerUser($entity);
		}
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function postUpdate(LifecycleEventArgs $args)
	{
		$this->em = $args->getEntityManager();
		$entity = $args->getEntity();
		if ($entity instanceof LoanOfficer) {
			$this->addLoanOfficerUser($entity);
		}
	}

	/**
	 * @param LoanOfficer $loanOfficer
	 */
	protected function addLoanOfficerUser(LoanOfficer $loanOfficer)
	{
		$loUser = $loanOfficer->getUser();
		$loSite = $loanOfficer->getOfficerSite();
		$loCreateUser = $loanOfficer->getAutoCreateUser();

		if($loCreateUser && !isset($loUser) && isset($loSite)) {
			$loRole = $this->em->getRepository('SudouxCmsUserBundle:Role')->findOneBy(array('role' => 'ROLE_LOAN_OFFICER'));
			// check if user already exists by email
			$existingUser = $this->em->getRepository('SudouxCmsUserBundle:User')->findOneBy(array('email' => $loanOfficer->getEmail()));
			if(isset($existingUser)) {
				// assign existing user to lo
				$loanOfficer->setUser($existingUser);
				$this->em->persist($loanOfficer);
			} else {
				$user = new User();
				$username = strtolower(substr($loanOfficer->getFirstName(), 0 , 1) . $loanOfficer->getLastName());
				$username = str_replace(' ', '', preg_replace("/[^A-Za-z0-9 ]/", '', $username));
				$user->setUsername($this->getLoanOfficerUsername($username));

				$factory = $this->container->get('security.encoder_factory');
				$encoder = $factory->getEncoder($user);
				$password = $encoder->encodePassword($user->generatePassword(), $user->getSalt());
				$user->setPassword($password);

				$user->setEmail($loanOfficer->getEmail());
				$user->setFirstName($loanOfficer->getFirstName());
				$user->setLastName($loanOfficer->getLastName());
				$user->addSite($loanOfficer->getOfficerSite());
				$user->addRole($loRole);
				$user->addToken();
				$user->setTimezone($loanOfficer->getSite()->getTimezone());

				$loanOfficer->setUser($user);
				$this->em->persist($loanOfficer);

				$emailUtil = $this->container->get('sudoux.cms.message.email_util');
                $email = new Email();
                $email->setRecipient($user->getEmail());
                $email->setRecipientName($user->getFullName());
                $email->setSubject($this->container->get('sudoux.cms.site')->getSiteVar('New Website Account', 'loan_officer_user_add_email_subject'));
                $email->setUser($user);
                $email->setSite($loanOfficer->getSite());

				$resetPasswordUrl = sprintf("https://%s%s", $loanOfficer->getOfficerSite()->getPrimaryDomain()->getDomain(), $this->router->generate('sudoux_cms_user_reset_password', array('token' => $user->getToken())));
				// default message
                $message = sprintf('<p>An account has been created for you on your new website. Your username is %s</p>', $user->getUsername());
                $message .= sprintf('<p><a href="%s">Click here</a> to set your password and confirm your account.</p>', $resetPasswordUrl);

				$tokens = array(
					'username' => $user->getUsername(),
					'email' => $user->getEmail(),
					'reset_password_url' => $resetPasswordUrl,
					'first_name' => $user->getFirstName(),
					'last_name' => $user->getLastName(),
					'website_url' => 'https://' . $loanOfficer->getOfficerSite()->getPrimaryDomain()->getDomain(),
				);

				$message = $this->container->get('sudoux.cms.site')->getSiteVar($message, 'loan_officer_user_add_email_message', $tokens);

				$email->setMessage($message);
                $emailUtil->logAndSend($email);
			}
			$this->em->flush($loanOfficer);
		}
	}

	protected function getLoanOfficerUsername($username, $i = 1)
	{
		$userEntity = $this->em->getRepository('SudouxCmsUserBundle:User')->findOneBy(array('username' => $username));
		if(isset($userEntity)) {
			$username .= $i;
			$username = $this->getLoanOfficerUsername($username, $i++);
		}

		return $username;
	}
}