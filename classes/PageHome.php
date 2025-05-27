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
						['account_stats', 'logged_in' => true],
						['learnlanguages', 'logged_in' => true],
						['login', 'logged_in' => false],
						['random_postcard', 'make_section' => true, 'section_header' => 'Random postcard', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['development_news', 'logged_in' => true],
						['site_news', 'logged_in' => true],
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