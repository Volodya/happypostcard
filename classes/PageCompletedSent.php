<?php

class PageCompletedSent extends Page_Abstract
{
	public function __construct(array $additionalUrl)
	{
		$user = User::constructByLogin($additionalUrl[0]);
		$this->templated =
 			(new Template('page', ['user' => $user, 'additional_title' => $user->getLogin()]))
				->withLeft(
					[
						[
							'user_statistics',
							'make_section' => true,
							'parameter' => ['user' => $user],
						],
						['user_main_image', 'make_section' => true],
					]
				)
				->withRight(
					[
						[
							'sent_postcards',
							'make_section' => true,
							'section_header' => 'Sent postcards'
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