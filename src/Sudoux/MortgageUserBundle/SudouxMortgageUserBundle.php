<?php

namespace Sudoux\MortgageUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SudouxMortgageUserBundle extends Bundle
{
	public function getParent()
	{
		return 'SudouxCmsUserBundle';
	}
}
