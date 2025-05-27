<?php

class PageRegister extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page'))
				->withLeft(
					[
						['gethelp'],
					]
				)
				->withRight(
					[
						['registration'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}