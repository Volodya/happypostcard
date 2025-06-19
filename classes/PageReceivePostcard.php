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
						[
							'user_statistics',
							'make_section' => true,
							'logged_in' => true,
						],
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