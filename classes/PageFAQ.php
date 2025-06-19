<?php

class PageFAQ extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'Frequently Asked Questions']))
				->withLeft(
					[
						['login', 'logged_in' => false],
						[
							'user_statistics',
							'make_section' => true,
							'logged_in' => true,
						],
						['random_card', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['faq', 'make_section' => true, 'section_header' => 'Frequently Asked Questions'],
						['how2help', 'make_section' => true, 'section_header' => 'How to contribute'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}