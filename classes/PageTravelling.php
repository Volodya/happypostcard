<?php

class PageTravelling extends Page_Abstract
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
					]
				)
				->withRight(
					[
						['travelling_postcards', 'logged_in' => true],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}