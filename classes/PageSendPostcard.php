<?php

class PageSendPostcard extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page'))
				->withLeft(
					[
						['gethelp'],
						[
							'user_statistics',
							'make_section' => true,
							'logged_in' => true,
						],
					]
				)
				->withRight(
					[
						['sendpostcard_note'],
						['sendpostcard'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}