<?php

class PageWPDCards extends Page_Abstract
{
	public function __construct()
	{
		$this->templated = 
			(new Template('page'))
				->withLeft(
					[
						['account', 'logged_in' => true],
						[
							'user_statistics',
							'make_section' => true,
							'logged_in' => true,
						],
						['learnlanguages', 'logged_in' => true],
						['login', 'logged_in' => false],
						//['random_postcard', 'make_section' => true, 'section_header' => 'Random postcard', 'logged_in' => true],
					]
				)
				->withRight(
					[
						[
							'text',
							'make_section' => true,
							'section_header' => 'World Postcard Day',
							'parameter' =>
								'World Postcard Day (WPD) is celebrated on the First of October of every year. '.
								'If you send your postcard on this day, it will be displayed on this page once it arrives.'
						],
						[
							'world_postcard_day_postcards',
							'make_section' => true,
							'section_header' => 'Happy Postcards sent on &laquo;World Postcard Day&raquo;',
						],
					]
				)
				->withBottom(
					[
						//['latestpostcards'],
					]
				);
	}
}