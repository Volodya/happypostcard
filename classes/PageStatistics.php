<?php

class PageStatistics extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'Statistics']))
				->withLeft(
					[
						['account_stats', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['statistics'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}