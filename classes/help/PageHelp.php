<?php

class PageHelp extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'Help']))
				->withLeft(
					[
						['login', 'logged_in' => false],
						[
							'user_statistics',
							'make_section' => true,
							'logged_in' => true,
						],
					]
				)
				->withRight(
					[
						['gethelp'],
						['help_info', 'make_section' => true],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}