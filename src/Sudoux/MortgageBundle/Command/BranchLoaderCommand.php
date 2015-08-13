<?php

namespace Sudoux\MortgageBundle\Command;

use Sudoux\Cms\LocationBundle\Entity\Location;
use Sudoux\MortgageBundle\Entity\Branch;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sudoux\Cms\FileBundle\Entity\File;
use Doctrine\ORM\NonUniqueResultException;
/**
 * Class BranchAddCommand
 * @package Sudoux\MortgageBundle\Command
 * @author Eric Haynes
 */
class BranchLoaderCommand extends ContainerAwareCommand
{
    protected $em;
    protected $site;
    protected $output;
    protected $uploadPath;
    protected $dialog;


    protected function configure()
    {
        $this
            ->setName('sudoux:mortgage:branch:loader')
            ->setDescription('Bulk Branch Uploader')
            ->addArgument('site',
                InputArgument::REQUIRED,
                "Site To Operate Branch Functions On");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->output = $output;
        $this->dialog = $dialog = $this->getHelperSet()->get('dialog');
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $siteId = (int)$input->getArgument('site');
        $this->site = $this->em->getRepository('SudouxCmsSiteBundle:Site')->find($siteId);


        $path2 = $this->getContainer()->get('kernel')->getRootDir().'/../';
        $this->uploadPath = realpath($path2)."/web/uploads/sites/$siteId/private";


        if (!isset($this->site)) {
            throw new \Exception("Site was not found");
            Exit(0);
        }

        $exit = true;


        do{

            $selection = $dialog->ask(
                $output,
                PHP_EOL.PHP_EOL."1.) Inflate ZIP".PHP_EOL.
                "2.) Check Header of CSV".PHP_EOL.
                "3.) Import CSV".PHP_EOL.
                "4.) Import Branch Pics".PHP_EOL.
                "5.) Exit".PHP_EOL,
                '1'
            );

            switch($selection) {

                case '1':
                    $this->inflate();
                    break;

                case '2':
                    $this->checkHeader();
                    break;

                case '3':
                    $this->importBranches();
                    break;

                case '4':
                    $this->importPics();
                    break;

                case '5':
                    $exit = false;

            }

        }while($exit);

        exit(0);

    }

    public function inflate(){

        $uploadDir = $this->uploadPath;
        chdir($uploadDir);

        $fileName = $this->dialog->ask(
            $this->output,
            'Please Enter The Name Of The ZIP File Containing the Branch CSV (branches.zip) - ',
            'branches.zip'
        );

        if( file_exists($fileName)){

            if (!$this->dialog->askConfirmation(
                $this->output,
                PHP_EOL."Do you want to unzip <fg=yellow>$fileName</fg=yellow> from <question>$uploadDir?</question>?  ",
                false
            )
            ) {
                return;
            }

            if( !file_exists($uploadDir."/branches/")) {

                if (!mkdir("branches", 0777, true)) {
                    die('Failed to create folder...');
                }

            }

            passthru("unzip $fileName -d branches");
            chdir($uploadDir."/branches");

        }else{

            $this->output->writeln("sorry, $fileName does not exist");

        }

    }


    protected function checkHeader(){



        $this->output->writeln("Available Files:<fg=yellow>".PHP_EOL);
        chdir($this->uploadPath."/branches");
        passthru("ls *.csv");

        $csvPick = $this->dialog->ask(
            $this->output,
            PHP_EOL.'</fg=yellow>Please Enter The Name Of The CSV You Want To Process (branches.csv) - ',
            'branches.csv'
        );

        if(file_exists($this->uploadPath."/branches/".$csvPick)){
            $file = fopen($csvPick,"r");
            print_r(fgetcsv($file));
            fclose($file);
        }


    }


    protected function importBranches(){
        $x = 0;
        $counter = 0;
        $zipPicRoot = $this->uploadPath."/branches/";
        $baseRoot = $this->getContainer()->get('kernel')->getRootDir().'/../';

        $this->output->writeln("Available Files: ".PHP_EOL);
        chdir($this->uploadPath."/branches");
        passthru("ls *.csv");

        $csvPick = $this->dialog->ask(
            $this->output,
            PHP_EOL.'</fg=yellow>Please Enter The Name Of The CSV You Want To Process (branch.csv) - ',
            'branch.csv'
        );

        $userName = $this->dialog->ask(
            $this->output,
            'Please Enter The User Name To Upload Pics As (admin)',
            'admin'
        );
        $user = $this->em->getRepository('SudouxCmsUserBundle:User')->loadUserByUsername($userName);


        if(file_exists($this->uploadPath."/branches/".$csvPick)){

            $this->output->writeln("  File Found: ".PHP_EOL);

            $file = fopen($csvPick,"r");

            //$this->output->writeln(implode(" - ",fgetcsv($file)));
            print_r(fgetcsv($file));

            $picKey = $this->dialog->ask(
                $this->output,
                'Which Field(#) Do you want to key the pic off of (1)?',
                1
            );

            while (($newBranchInfo = fgetcsv($file)) !== FALSE){
                $newBranch = New Branch();
                $newLocation = New Location();
                $x++;
                $this->output->writeln("    Adding Branch $x:  ".PHP_EOL);
                $this->output->writeln(implode(" - ",$newBranchInfo));
                if (!$this->dialog->askConfirmation(
                    $this->output,
                    PHP_EOL."Are You Sure You Want To Add This Site?</question>?  ",
                    false
                )
                ) {
                    return;
                }
                $newBranch->setSite($this->site);
                $newBranch->setName($newBranchInfo[0]);
                $newBranch->setNmlsId($newBranchInfo[1]);
                $newBranch->setPhone($newBranchInfo[2]);
                $newBranch->setFax($newBranchInfo[3]);
                $newBranch->setEmail($newBranchInfo[4]);
                $newBranch->setLosId($newBranchInfo[6]);

                    $newLocation->setAddress1($newBranchInfo[7]);
                    $newLocation->setCity($newBranchInfo[8]);
                        $stateLookup = $this->em->getRepository('SudouxCmsLocationBundle:State')->findByName($newBranchInfo[9]);
                       // print_r($stateLookup['0']);
                    $newLocation->setState($stateLookup['0']);
                    $newLocation->setZipcode($newBranchInfo[10]);

                $this->em->persist($newLocation);

                $globResults = glob("$this->uploadPath/branches/$newBranchInfo[$picKey].*");
                $ext = pathinfo($globResults[0], PATHINFO_EXTENSION);
                if($globResults){

                    $savePath = realpath($baseRoot)."/web/uploads/sites/".$this->site->getId()."/public/";
                    $picName = "$newBranchInfo[$picKey].$ext";
                    $uploadedPicPath = $globResults[0];
                    $MIMEtype = mime_content_type ( $uploadedPicPath);

                    while(file_exists($savePath.$picName)){

                        $picName = $newBranchInfo[$picKey]."_$counter.".$ext;
                        $counter++;
                        $this->output->writeln("File Name Exists - New Name: <fg=blue>$picName</fg=blue>");

                    }

                    $openFileName = $zipPicRoot.$picName;
                    rename($openFileName ,$savePath.$picName );
                    $photo = new File();
                    $size = filesize($savePath.$picName);
                    $photo->setPath("uploads/sites/".$this->site->getId()."/public/".$picName);
                    $photo->setFilesize($size);
                    $photo->setName( $newBranchInfo[0]);
                    $photo->setUser($user);
                    $photo->setMimeType($MIMEtype);
                    $photo->setSite($this->site);
                    $this->em->persist($photo);
                    $newBranch->setBranchPhoto($photo);


                }




                $newBranch->setLocation($newLocation);
                $newBranch->setActive($newBranchInfo[11]);
                $newBranch->setWebsite($newBranchInfo[12]);
                $this->em->persist($newBranch);

                $this->em->flush();

            }
            fclose($file);



        }


    }

    protected function importPics(){


        $x = 0;
        $counter = 0;
        $zipPicRoot = $this->uploadPath."/branches/";
        $baseRoot = $this->getContainer()->get('kernel')->getRootDir().'/../';

        $this->output->writeln("Available Files: ".PHP_EOL);
        chdir($this->uploadPath."/branches");
        passthru("ls *.csv");

        $csvPick = $this->dialog->ask(
            $this->output,
            PHP_EOL.'</fg=yellow>Please Enter The Name Of The CSV You Want To Process (branch.csv) - ',
            'branch.csv'
        );

        $userName = $this->dialog->ask(
            $this->output,
            'Please Enter The User Name To Upload Pics As (admin)',
            'admin'
        );
        $user = $this->em->getRepository('SudouxCmsUserBundle:User')->loadUserByUsername($userName);


        if(file_exists($this->uploadPath."/branches/".$csvPick)){

            $this->output->writeln("  File Found: ".PHP_EOL);

            $file = fopen($csvPick,"r");

           // $this->output->writeln(implode(" - ",fgetcsv($file)));

            print_r(fgetcsv($file));

            $picKey = $this->dialog->ask(
                $this->output,
                'Which Field(#) Do you want to key the pic off of (1)?',
                1
            );

            while (($newBranchInfo = fgetcsv($file)) !== FALSE){
                $x++;

                $globResults = glob("$this->uploadPath/branches/$newBranchInfo[$picKey].*");

                if($globResults){
                    $ext = pathinfo($globResults[0], PATHINFO_EXTENSION);
                    $this->output->writeln(PHP_EOL.implode(" - ",$newBranchInfo));

                    if (!$this->dialog->askConfirmation(
                        $this->output,
                        PHP_EOL."Are You Sure You Want To Add Pic( $globResults[0]) To This Branch?</question>?  ",
                        false
                    )
                    ) {
                        return;
                    }

                    $savePath = realpath($baseRoot)."/web/uploads/sites/".$this->site->getId()."/public/";
                    $picName = "$newBranchInfo[$picKey].$ext";
                    $uploadedPicPath = $globResults[0];
                    $MIMEtype = mime_content_type ( $uploadedPicPath);

                    while(file_exists($savePath.$picName)){

                        $picName = $newBranchInfo[$picKey]."_$counter.".$ext;
                        $counter++;
                        $this->output->writeln("File Name Exists - New Name: <fg=blue>$picName</fg=blue>");

                    }

                    $openFileName = $zipPicRoot.$picName;
                    rename($openFileName ,$savePath.$picName );
                    $photo = new File();
                    $size = filesize($savePath.$picName);
                    $photo->setPath("uploads/sites/".$this->site->getId()."/public/".$picName);
                    $photo->setFilesize($size);
                    $photo->setName( $newBranchInfo[0]);
                    $photo->setUser($user);
                    $photo->setMimeType($MIMEtype);
                    $photo->setSite($this->site);
                    $this->em->persist($photo);


                    try{
                        $updateBranch = $this->em->getRepository('SudouxMortgageBundle:Branch')->findOneBySiteAndNmlsId($this->site, $newBranchInfo[1]);
                        If(isset($updateBranch)){

                            $this->output->writeln("Branch Exists - Updating Now");
                            $updateBranch->setBranchPhoto($photo);
                            $this->em->persist($updateBranch);

                        }else{

                            $this->output->writeln("Branch Does Not Exist - Not Updating");
                        }

                    }catch(NonUniqueResultException $e){

                        $this->output->writeln("More Than One Branch By This NMLS Number Exists - Not Updating");

                    }

                    $this->em->flush();
                }


            }
            fclose($file);



        }



    }


}
