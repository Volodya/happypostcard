<?php

class PageRecoverPassword extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'Password recovery']))
				->withLeft(
					[
						['gethelp', 'logged_in' => false],
					]
				)
				->withRight(
					[
						['passwordrecovery_info'],
						['recoverpass_step1', 'logged_in' => false],
						['recoverpass_step2', 'logged_in' => false],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}