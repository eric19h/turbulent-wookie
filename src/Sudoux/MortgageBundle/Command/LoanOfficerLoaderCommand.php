<?php

namespace Sudoux\MortgageBundle\Command;

use Symfony\Component\HttpFoundation\Request;
use Sudoux\Cms\FileBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LoPicCommand
 * @package Sudoux\MortgageBundle\Command
 * @author Eric Haynes
 */
class LoanOfficerLoaderCommand extends ContainerAwareCommand
{
    protected $em;
    protected $site;
    protected $output;
    protected $uploadPath;
    protected $dialog;

    //   protected $dialog;

    protected function configure()
    {
        $this
            ->setName('sudoux:mortgage:loanofficer:loader')
            ->setDescription('Loan officer picture utility')
            ->addArgument('site',
                InputArgument::REQUIRED,
                "Site To Operate Picture Functions On");
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
                PHP_EOL.PHP_EOL."1.) Inflate".PHP_EOL.
                "2.) Add Pics - NMLS id".PHP_EOL.
                "3.) Add Pics - LOS id".PHP_EOL.
                "4.) Add Pics - Fist_Last".PHP_EOL.
                "5.) Exit".PHP_EOL,
                '1'
            );

            switch($selection) {

                case '1':
                    $this->inflate();
                    break;

                case '2':
                    $this->addPicNmls();
                    break;

                case '3':
                    $this->addPicLos();
                    break;

                case '4':
                    $this->addPicName();
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
            'Please Enter The Name Of The ZIP File Containing Pics (pictures.zip) - ',
            'pictures.zip'
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

            if( !file_exists($uploadDir."/pics/")) {

                if (!mkdir("pics", 0777, true)) {
                    die('Failed to create folder...');
                }

            }

            passthru("unzip $fileName -d pics");
            chdir($uploadDir."/pics");

        }else{

            $this->output->writeln("sorry, $fileName does not exist");

        }

    }


    public function addPicNmls(){

        $x = 0;
        $em = $this->em;
        $site = $this->site;
        $zipPicRoot = $this->uploadPath."/pics/";
        $baseRoot = $this->getContainer()->get('kernel')->getRootDir().'/../';


        chdir($zipPicRoot);

        $userName = $this->dialog->ask(
            $this->output,
            'Please Enter The User Name To Upload As (admin)',
            'admin'
        );


        $user = $this->em->getRepository('SudouxCmsUserBundle:User')->loadUserByUsername($userName);
        $officers = $this->em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySite($site);


        foreach($officers as $lo){
            $x++;
            $counter = 0;
            $size = 0;
            $photoData = NULL;
            $loNmls = $lo->getNmlsId();

            $globResults = glob("$zipPicRoot$loNmls.*");

            if( $globResults ){

                $this->output->writeln(PHP_EOL."<fg=yellow>$x.) Picture Found for NMLSid -</fg=yellow> <fg=blue>$loNmls</fg=blue>".PHP_EOL);
                $ext = pathinfo($globResults[0], PATHINFO_EXTENSION);
                $picName = "$loNmls.$ext";
                $existingPhoto = $lo->getOfficerPhoto();

                if(isset($existingPhoto)) {
                    $em->remove($existingPhoto);
                }

                $openFileName = $zipPicRoot.$picName;
                $savePath = realpath($baseRoot)."/web/uploads/sites/".$site->getId()."/public/";
                $MIMEtype = mime_content_type ( $openFileName);

                $this->output->writeln("Current Name: ".$picName);
                $this->output->writeln("SavePath: ".$savePath);
                $this->output->writeln("MIME Type: ".$MIMEtype);

                while(file_exists($savePath.$picName)){

                    $picName = $loNmls."_$counter.".$ext;
                    $counter++;
                    $this->output->writeln("File Name Exists - New Name: <fg=blue>{$picName}</fg=blue>");

                }

                rename($openFileName ,$savePath.$picName );
                $photo = new File();
                $size = filesize($savePath.$picName);
                $photo->setPath("uploads/sites/".$site->getId()."/public/".$picName);
                $photo->setFilesize($size);
                $photo->setName( $lo->getFullName() );
                $photo->setUser($user);
                $photo->setMimeType($MIMEtype);
                $photo->setSite($site);
                $this->em->persist($photo);

                $lo->setOfficerPhoto($photo);

                $this->em->persist($lo);

                $this->output->writeln("<fg=yellow>".$lo->getFullName()."</fg=yellow> <fg=green>- photo has been updated</fg=green>");

            }else{

                $this->output->writeln(PHP_EOL."$x.) No photo found for ". $lo->getFullName() );

            }

        }

        $em->flush();


        $this->output->writeln(PHP_EOL."<fg=yellow>     The Following Pictures Were Unable To Be Set as Officer Photos - </fg=yellow>");
        chdir($zipPicRoot);
        passthru("ls");


    }


    public function addPicLos(){

        $x = 0;
        $em = $this->em;
        $site = $this->site;
        $zipPicRoot = $this->uploadPath."/pics/";
        $baseRoot = $this->getContainer()->get('kernel')->getRootDir().'/../';


        chdir($zipPicRoot);

        $userName = $this->dialog->ask(
            $this->output,
            'Please Enter The User Name To Upload As (admin)',
            'admin'
        );


        $user = $this->em->getRepository('SudouxCmsUserBundle:User')->loadUserByUsername($userName);
        $officers = $this->em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySite($site);


        foreach($officers as $lo){
            $x++;
            $counter = 0;
            $size = 0;
            $photoData = NULL;
            $loLOS = $lo->getLosId();

            $globResults = glob("{$zipPicRoot}{$loLOS}.*");

            if( $globResults ){

                $this->output->writeln(PHP_EOL."$x.) Picture Found for LOSid - $loLOS".PHP_EOL);

                $ext = pathinfo($globResults[0], PATHINFO_EXTENSION);
                $picName = "$loLOS.$ext";
                $existingPhoto = $lo->getOfficerPhoto();

                if(isset($existingPhoto)) {
                    $em->remove($existingPhoto);
                }


                $openFileName = $zipPicRoot.$picName;
                $savePath = realpath($baseRoot)."/web/uploads/sites/".$site->getId()."/public/";
                $MIMEtype = mime_content_type ( $openFileName);

                $this->output->writeln("Current Name: ".$picName);
                $this->output->writeln("SavePath: ".$savePath);
                $this->output->writeln("MIME Type: ".$MIMEtype);

                while(file_exists($savePath.$picName)){
                    $picName = $loLOS."_$counter.".$ext;
                    $counter++;
                    $this->output->writeln("File Name Exists - New Name: <fg=blue>$picName</fg=blue>");

                }

                rename($openFileName ,$savePath.$picName );
                $photo = new File();
                $size = filesize($savePath.$picName);
                $photo->setPath("uploads/sites/".$site->getId()."/public/".$picName);
                $photo->setFilesize($size);
                $photo->setName( $lo->getFullName() );
                $photo->setUser($user);
                $photo->setMimeType($MIMEtype);
                $photo->setSite($site);
                $this->em->persist($photo);

                $lo->setOfficerPhoto($photo);

                $this->em->persist($lo);

                $this->output->writeln($lo->getFullName()." - photo has been updated");

            }else{

                $this->output->writeln(PHP_EOL."$x.) No photo found for ". $lo->getFullName() );

            }

        }

        $em->flush();

        $this->output->writeln(PHP_EOL."<fg=yellow>     The Following Pictures Were Unable To Be Set as Officer Photos - </fg=yellow>");
        chdir($zipPicRoot);
        passthru("ls");

    }


    public function addPicName(){

        $x = 0;
        $em = $this->em;
        $site = $this->site;
        $zipPicRoot = $this->uploadPath."/pics/";
        $baseRoot = $this->getContainer()->get('kernel')->getRootDir().'/../';


        chdir($zipPicRoot);

        $userName = $this->dialog->ask(
            $this->output,
            'Please Enter The User Name To Upload As (admin)',
            'admin'
        );


        $user = $this->em->getRepository('SudouxCmsUserBundle:User')->loadUserByUsername($userName);
        $officers = $this->em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySite($site);


        foreach($officers as $lo){
            $x++;
            $counter = 0;
            $size = 0;
            $photoData = NULL;
            $loName = str_replace(" ", "_", $lo->getFullName());

            $globResults = glob("$zipPicRoot$loName.*");

            if( $globResults ){

                $this->output->writeln(PHP_EOL."$x.) Picture Found for LOSid - $loName".PHP_EOL);

                $ext = pathinfo($globResults[0], PATHINFO_EXTENSION);
                $picName = "$loName.$ext";
                $existingPhoto = $lo->getOfficerPhoto();

                if(isset($existingPhoto)) {
                    $em->remove($existingPhoto);
                }


                $openFileName = $zipPicRoot.$picName;
                $savePath = realpath($baseRoot)."/web/uploads/sites/".$site->getId()."/public/";
                $MIMEtype = mime_content_type ( $openFileName);

                $this->output->writeln("Current Name: ".$picName);
                $this->output->writeln("SavePath: ".$savePath);
                $this->output->writeln("MIME Type: ".$MIMEtype);

                while(file_exists($savePath.$picName)){
                    $picName = $loName."_$counter.".$ext;
                    $counter++;
                    $this->output->writeln("File Name Exists - New Name: <fg=blue>$picName</fg=blue>");

                }

                rename($openFileName ,$savePath.$picName );
                $photo = new File();
                $size = filesize($savePath.$picName);
                $photo->setPath("uploads/sites/".$site->getId()."/public/".$picName);
                $photo->setFilesize($size);
                $photo->setName( $lo->getFullName() );
                $photo->setUser($user);
                $photo->setMimeType($MIMEtype);
                $photo->setSite($site);
                $this->em->persist($photo);

                $lo->setOfficerPhoto($photo);

                $this->em->persist($lo);

                $this->output->writeln($lo->getFullName()." - photo has been updated");

            }else{

                $this->output->writeln(PHP_EOL."$x.) No photo found for ". $lo->getFullName() );

            }

        }

        $em->flush();

        $this->output->writeln(PHP_EOL."<fg=yellow>     The Following Pictures Were Unable To Be Set as Officer Photos - </fg=yellow>");
        chdir($zipPicRoot);
        passthru("ls");

    }

}