<?php

namespace Sudoux\MortgageBundle\Command;

use Sudoux\MortgageBundle\DependencyInjection\LosUtil;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\Null;

/**
 * Class ResetLoanCommand
 * @package Sudoux\MortgageBundle\Command
 * @author Dan Alvare
 */
class ResetLoanCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sudoux:mortgage:resetloan')
            ->setDescription('Resets a completed loan for testing')
            ->addArgument(
                'loanId',
                InputArgument::REQUIRED,
                'Loan ID to reset'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loanId= $input->getArgument('loanId');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $loan = $em->getRepository('SudouxMortgageBundle:LoanApplication')->find($loanId);

        $loan->setLosModified(NULL);
        $loan->setSentToLos(0);
        $loan->setLockStatus(0);
        $loan->setMilestoneGroup(NULL);
        $loan->setMilestone(NULL);
        $loan->setLosId(NULL);
        $loan->setLastStepCompleted(6);


        $em->persist($loan);
        $em->flush();

        $output->writeln("Loan Has Been Reset!");
    }
}