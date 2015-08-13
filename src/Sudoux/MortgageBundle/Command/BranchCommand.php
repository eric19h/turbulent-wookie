<?php

namespace Sudoux\MortgageBundle\Command;

use Sudoux\Cms\LocationBundle\Entity\Location;
use Sudoux\MortgageBundle\DependencyInjection\LosUtil;

use Sudoux\MortgageBundle\Entity\Branch;
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
 * Class BranchCommand
 * @package Sudoux\MortgageBundle\Command
 * @author Dan Alvare
 */
class BranchCommand extends ContainerAwareCommand
{
    protected $em;
    protected $site;
    protected $output;

    protected function configure()
    {
        $this
            ->setName('sudoux:mortgage:branch')
            ->setDescription('Branch utility')
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
            'import_branches_from_csv',
        );

        $function = $input->getArgument('function');

        if(!in_array($function, $availableFunctions)) {
            throw new \Exception("Function does not exist");
        }

        switch($function) {
            case 'import_branches_from_csv':
                $csvPath = $input->getOption('csv_path');
                if(empty($csvPath)) {
                    throw new \Exception("Option --csv_path cannot be empty");
                }
                $this->importBranchesFromCsv($csvPath);
                break;
        }
    }

    protected function importBranchesFromCsv($csvPath)
    {
        ini_set('auto_detect_line_endings', true);

        $siteId = $this->site->getId();

        $states = $this->em->getRepository('SudouxCmsLocationBundle:State')->findAll();

        $header = array('name','nmls_id','los_id','phone','fax','email','address1','address2','unit','city','state','zipcode','latitude','longitude','directions','description');
        $headerValid = true;

        $batchCount = 10;

        if(file_exists($csvPath)) {


            if (($handle = fopen($csvPath, "r")) !== FALSE) {
                $row = 0;

                $states = $this->em->getRepository('SudouxCmsLocationBundle:State')->findAll();

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
                        $branch = new Branch();
                        $branch->setName($this->getCsvValue($data[0]));
                        $branch->setNmlsId($this->getCsvValue($data[1]));
                        $branch->setLosId($this->getCsvValue($data[2]));
                        $branch->setPhone($this->getCsvValue($data[3]));
                        $branch->setFax($this->getCsvValue($data[4]));
                        $branch->setEmail($this->getCsvValue($data[5]));

                        $location = new Location();
                        $location->setAddress1($this->getCsvValue($data[6]));
                        $location->setAddress2($this->getCsvValue($data[7]));
                        $location->setUnit($this->getCsvValue($data[8]));
                        $location->setCity($this->getCsvValue($data[9]));

                        foreach ($states as $state) {
                            if ($state->getAbbreviation() == trim($data[10]) || $state->getName() == trim($data[10])) {
                                $location->setState($state);
                                break;
                            }
                        }

                        $location->setZipcode($this->getCsvValue($data[11]));
                        $location->setLatitude($this->getCsvValue($data[12]));
                        $location->setLongitude($this->getCsvValue($data[13]));

                        $branch->setLocation($location);

                        $branch->setDirections($this->getCsvValue($data[14]));
                        $branch->setDescription($this->getCsvValue($data[15]));
                        $branch->setSite($this->site);

                        $this->em->persist($branch);

                        if (($row % $batchCount) == 0) {
                            $this->em->flush();
                            $this->em->clear();
                            $this->site = $this->em->getRepository('SudouxCmsSiteBundle:Site')->find($siteId);
                            $states = $this->em->getRepository('SudouxCmsLocationBundle:State')->findAll();
                            $this->output->writeln(sprintf('%s rows processed', $row));
                        }
                    }
                    $row++;
                }

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