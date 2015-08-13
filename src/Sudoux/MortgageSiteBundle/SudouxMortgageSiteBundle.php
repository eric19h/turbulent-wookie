<?php

namespace Sudoux\MortgageSiteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SudouxMortgageSiteBundle extends Bundle
{
    public function getParent()
    {
        return 'SudouxCmsSiteBundle';
    }
}
