<?php

namespace Sudoux\EagleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * Class SudouxEagleBundle
 * @package Sudoux\EagleBundle
 * @author Eric Haynes
 */
class SudouxEagleBundle extends Bundle
{

    /**
     * @author Eric Haynes
     */
    public function getParent(){

        return 'SudouxMortgageBundle';

    }



}
