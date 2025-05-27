<?php

class PageSendBirthdayCard extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'Send birthday card']))
				->withLeft(
					[
						['account_stats', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['sendpostcard_note'],
						['select_birthday_recepient', 'make_section' => true, 'section_header' => 'Birthdays'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}