<?php

class PageListOfUsersComplete extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', []))
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