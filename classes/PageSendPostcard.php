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
						['account_stats', 'logged_in' => true],
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