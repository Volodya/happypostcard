<?php

class PageReceivePostcard extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'Receive a postcard']))
				->withLeft(
					[
						['gethelp'],
						['account_stats', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['receivepostcard'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}