<?php

class PageStatistics extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'Statistics']))
				->withLeft(
					[
						[
							'user_statistics',
							'make_section' => true,
							'parameter' => ['user' => $user],
						],
					]
				)
				->withRight(
					[
						[
							'site_statistics',
							'make_section' => true,
							'section_header' => 'Happy Postcard statistics',
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