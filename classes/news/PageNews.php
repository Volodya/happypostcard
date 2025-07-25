<?php

class PageNews extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page'))
				->withLeft(
					[
						['login', 'logged_in' => false],
						['account', 'logged_in' => true],
						['account_stats', 'logged_in' => true],
					]
				)
				->withRight(
					[
						[
							'user_news',
							'logged_in' => true,
							'make_section' => true,
							'section_header' => 'User news',
						],
						[
							'site_news',
							'logged_in' => false,
							'make_section' => true,
							'section_header' => 'Site News',
						],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}