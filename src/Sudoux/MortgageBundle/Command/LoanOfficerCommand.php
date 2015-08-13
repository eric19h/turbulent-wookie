<?php

namespace Sudoux\MortgageBundle\Command;

use Sudoux\MortgageBundle\DependencyInjection\LosUtil;

use Sudoux\MortgageBundle\Entity\LoanOfficer;
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
class LoanOfficerCommand extends ContainerAwareCommand
{
    protected $em;
    protected $site;
    protected $output;

    protected function configure()
    {
        $this
            ->setName('sudoux:mortgage:loanofficer')
            ->setDescription('Loan officer utility')
            ->addArgument('function', InputArgument::REQUIRED)
            ->addOption('site_id',
                null,
                InputOption::VALUE_REQUIRED,
                'The site ID to run the command under'
            )
            ->addOption('csv_path',
                null,
                InputOption::VALUE_OPTIONAL,
                'The csv path'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', -1);
        set_time_limit(0);

        $this->output = $output;
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $siteId = (int)$input->getOption('site_id');
        $this->site = $this->em->getRepository('SudouxCmsSiteBundle:Site')->find($siteId);

        if(!isset($this->site)) {
            throw new \Exception("Site was not found");
        }

        $availableFunctions = array(
            'sync_loan_officers_with_los',
            'import_loan_officers_from_csv',
        );

        $function = $input->getArgument('function');

        if(!in_array($function, $availableFunctions)) {
            throw new \Exception("Function does not exist");
        }

        switch($function) {
            case 'sync_loan_officers_with_los':
                $this->syncLoanOfficersWithLos($siteId);
                break;
            case 'import_loan_officers_from_csv':
                $csvPath = $input->getOption('csv_path');
                if(empty($csvPath)) {
                    throw new \Exception("Option --csv_path cannot be empty");
                }
                $this->importLoanOfficersFromCsv($csvPath);
                break;
        }
    }

    /**
     *
     */
    protected function syncLoanOfficersWithLos($siteId)
    {
        $loanUtil = $this->getContainer()->get('sudoux_mortgage.los_util');
        $response = $loanUtil->getLoanOfficers($this->site);
        $batch = 10;
        //print_r($response['loanOfficers']);exit;
        if($response['success']) {
            $this->output->writeln(sprintf('processing %s loan officers', count($response['loanOfficers'])));
            $losLoanOfficers = $response['loanOfficers'];
            $i = 0;
            //print_r($response['loanOfficers']); exit;
            foreach ($response['loanOfficers'] as $loData) {
                $loanOfficer = $this->em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBySiteAndLosId($this->site, $loData['username']);
                if (!isset($loanOfficer)) {
                    $loanOfficer = new LoanOfficer();
                    $loanOfficer->setSite($this->site);
                    $loanOfficer->setLosId($loData['username']);
                }

                $loanOfficer->setFirstName($loData['firstName']);
                $loanOfficer->setLastName($loData['lastName']);
                $loanOfficer->setEmail($loData['email']);
                $loanOfficer->setPhoneMobile($loData['cellPhone']);
                $loanOfficer->setPhoneOffice($loData['phone']);
                $loanOfficer->setFax($loData['fax']);
                $loanOfficer->setNmlsId($loData['nmlsID']);
                $loanOfficer->setAutoCreateUser(false); // this needs to be set after setSite to override default functionality
                $loanOfficer->setActive(true);
                $loanOfficer->setDeleted(false);

                $loSite = $loanOfficer->getOfficerSite();
                if(isset($loSite)) {
                    $loSite->setActive(true);
                    $loSite->setDeleted(false);
                    $this->em->persist($loSite);
                }

                //print_r($loData);
                if(!empty($loData['orgID'])) {
                    // look up branch
                    $branch = $this->em->getRepository('SudouxMortgageBundle:Branch')->findOneBySiteAndLosId($this->site, $loData['orgID']);
                    if(isset($branch)) {
                        $loanOfficer->setBranch($branch);
                        $branchSite = $branch->getBranchSite();
                        if(isset($branchSite) && isset($loSite)) {
                            $loSite->setParentSite($branchSite);
                            $this->em->persist($loSite);
                        }
                    }
                }

                $this->em->persist($loanOfficer);

                if (($i % $batch) == 0) {
                    $this->em->flush();
                    $this->em->clear();
                    $this->site = $this->em->getRepository('SudouxCmsSiteBundle:Site')->find($siteId);
                    $this->output->writeln(sprintf('Batch %s of %s complete.', $i, count($response['loanOfficers'])));
                }

                $i++;
            }

            $this->em->flush();
            $this->em->clear();
            $this->output->writeln('Processing complete!');

        } else {
            $this->output->writeln('Failed to get a successful response from the LOS');
        }
    }

    /**
     * @param $csvPath
     * @throws \Exception
     */
    protected function importLoanOfficersFromCsv($csvPath)
    {
        ini_set('auto_detect_line_endings', true);

        $siteId = $this->site->getId();
        $header = array('first_name','last_name','email','los_id','nmls_id','title','phone_office','phone_mobile','phone_tollfree','fax','signature','bio','branch_nmls_id');

        $headerValid = true;
        $batchCount = 10;

        if(file_exists($csvPath)) {
            if (($handle = fopen($csvPath, "r")) !== FALSE) {
                $row = 0;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    if ($row == 0) {
                        // validate the header
                        for ($i = 0; $i < count($data); $i++) {
                            if ($data[$i] != $header[$i]) {
                                $headerValid = false;
                                break;
                            }
                        }

                        if (!$headerValid) {
                            throw new \Exception('Csv headers are not valid. Correct format is ' . implode(',', $header));
                        }

                    } else {

                        $loanOfficer = new LoanOfficer();
                        $loanOfficer->setFirstName($this->getCsvValue($data[0]));
                        $loanOfficer->setLastName($this->getCsvValue($data[1]));
                        $loanOfficer->setEmail($this->getCsvValue($data[2]));
                        $loanOfficer->setLosId($this->getCsvValue($data[3]));
                        $loanOfficer->setNmlsId($this->getCsvValue($data[4]));
                        $loanOfficer->setTitle($this->getCsvValue($data[5]));
                        $loanOfficer->setPhoneOffice($this->getCsvValue($data[6]));
                        $loanOfficer->setPhoneMobile($this->getCsvValue($data[7]));
                        $loanOfficer->setPhoneTollfree($this->getCsvValue($data[8]));
                        $loanOfficer->setFax($this->getCsvValue($data[9]));
                        $loanOfficer->setSignature($this->getCsvValue($data[10]));
                        $loanOfficer->setBio($this->getCsvValue($data[11]));
                        $loanOfficer->setSite($this->site);
                        $loanOfficer->setAutoCreateUser(false); // this needs to be set after setSite to override default functionality

                        // lookup branch
                        $branchNmlsId = $this->getCsvValue($data[12]);
                        if(!empty($branchNmlsId)) {
                            $branch = $this->em->getRepository('SudouxMortgageBundle:Branch')->findOneBySiteAndNmlsId($this->site, $branchNmlsId);

                            if(isset($branch)) {
                                $loanOfficer->setBranch($branch);
                            }
                        }

                        $this->em->persist($loanOfficer);

                        if (($row % $batchCount) == 0) {
                            $this->em->flush();
                            $this->em->clear();
                            $this->site = $this->em->getRepository('SudouxCmsSiteBundle:Site')->find($siteId);
                            $this->output->writeln(sprintf('%s rows processed', $row));
                        }
                    }
                    $row++;
                }
                exit;
                $this->em->flush();
                $this->em->clear();
                $this->output->writeln(sprintf('Processing complete! %s rows processed', $row));
            }
        }
    }

    /**
     * @param $value
     * @return null
     */
    protected function getCsvValue($value)
    {
        if(empty($value)) {
            $value = null;
        }

        return $value;
    }
}