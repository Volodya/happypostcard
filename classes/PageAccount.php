<?php

class PageAccount extends Page_Abstract
{
	public function __construct()
	{
		$this->templated = 
			(new Template('page'))
				->withLeft(
					[
						['account', 'logged_in' => true],
						['account_stats', 'logged_in' => true],
						['login', 'logged_in' => false],
					]
				)
				->withRight(
					[
						['password_change_form', 'logged_in' => true],
						['logout', 'logged_in' => true, 'make_section' => true, 'section_header' => 'Log out'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}