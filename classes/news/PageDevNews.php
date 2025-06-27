<?php

class PageDevNews extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'Development news']))
				->withLeft(
					[
						['login', 'logged_in' => false],
						['account', 'logged_in' => true],
						['account_stats', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['development_news'],
						[
							'text',
							'make_section' => true,
							'section_header' => 'Code',
							'parameter' =>
								'You can see the code for this website at <a href="https://github.com/Volodya/happypostcard">GitHub</a>.'
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