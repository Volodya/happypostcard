<?php

class PageLoginUser extends Page_Abstract
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
						['login'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}