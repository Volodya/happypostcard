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
						[
							'learn_languages',
							'make_section' => true,
							'section_header' => 'Learn languages',
							'logged_in' => true,
						],
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
						
						[
							'FAQ',
							'make_section' => true,
							'section_header' => 'From the FAQ',
							'parameter' => [
								'questions'=>[1],
							],
							'logged_in' => false,
						],
						[
							'FAQ',
							'make_section' => true,
							'section_header' => 'From the FAQ',
							'parameter' => [
								'questions'=>[2, 5, 11, 14, 15, 16, 17],
								'type'=>'random',
							],
							'logged_in' => true,
						],
					]
				)
				->withRight(
					[
						['sitedescription'],
						[
							'site_news',
							'logged_in' => true,
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