<?php

namespace Sudoux\MortgageApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SudouxMortgageApiBundle extends Bundle
{
    public function getParent()
    {
        return 'SudouxCmsApiBundle';
    }
}
