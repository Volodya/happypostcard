<?php

class PageHome extends Page_Abstract
{
	public function __construct()
	{
		$this->templated = 
			(new Template('page'))
				->withLeft(
					[
						/*[
							'text',
							'make_section' => true,
							'section_header' => 'WPD',
							'parameter' => 'Happy <a href="/wpd_cards">World Postcard Day</a>!',
						],*/
						['account', 'logged_in' => true],
						[
							'user_statistics',
							'logged_in' => true,
							'make_section' => true,
						],
						['learnlanguages', 'logged_in' => true],
						['login', 'logged_in' => false],
						['random_postcard', 'make_section' => true, 'section_header' => 'Random postcard', 'logged_in' => true],
						[
							'users_waitingapproval',
							'admin' => true,
							'make_section' => true,
							'section_header' => 'Waiting for aproval',
							'clear_on_false' => true,
							'parameter' => [],
						],
					]
				)
				->withRight(
					[
						['development_news', 'logged_in' => true],
						[
							'site_news',
							'logged_in' => true,
							'make_section' => true,
							'section_header' => 'Site News',
						],
						['sitedescription', 'logged_in' => false],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}