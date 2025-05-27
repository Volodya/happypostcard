<?php

class PageListOfUsers extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', []))
				->withLeft(
					[
						['login', 'logged_in' => false],
						['account_stats', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['list_of_users'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}