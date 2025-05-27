<?php

class PageCompletedReceived extends Page_Abstract
{
	public function __construct(array $additionalUrl)
	{
		$user = User::constructByLogin($additionalUrl[0]);
		$this->templated =
 			(new Template('page', ['user' => $user, 'additional_title' => $user->getLogin()]))
				->withLeft(
					[
						['account_stats_for_user'],
						['user_main_image', 'make_section' => true],
					]
				)
				->withRight(
					[
						[
							'received_postcards',
							'make_section' => true,
							'section_header' => 'Received postcards'
						],
					]
				)
				->withBottom(
					[
						[
							'queue' => [
								[
									'latestpostcards_interuser',
									'logged_in' => true,
									'make_section'=> true,
									'section_header' => 'Latest exchanges with this user',
									'clear_on_false' => true
								],
								['latestpostcards'],
							]
						]
					]
				);
	}
}