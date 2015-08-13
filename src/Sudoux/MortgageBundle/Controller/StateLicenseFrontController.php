<?php

namespace Sudoux\MortgageBundle\Controller;

use Sudoux\Cms\SiteBundle\Controller\FrontController;

/**
 * Class StateLicenseFrontController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class StateLicenseFrontController extends FrontController
{
    public function stateLicenseBlockAction()
    {
        $site = $this->get('sudoux.cms.site')->getSite();

        $licenses = $site->getSettings()->getInheritedStateLicense();

        return $this->render('SudouxMortgageBundle:StateLicenseFront:stateLicenseBlock.html.twig', array(
            'licenses' => $licenses,
        ));
    }
}