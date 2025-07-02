<?php

class PageUser extends Page_Abstract
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
							'section_header' => $user->getLogin(),
							'parameter' => ['user' => $user],
						],
						[
							'user_status',
							'logged_in' => true,
							'make_section' => true,
							'section_header' => 'Status',
							'clear_on_false' => true
						],
						['user_main_image', 'make_section' => true],
						[
							'address_waitingapproval',
							'admin' => true,
							'make_section' => true,
							'section_header' => 'Waiting for aproval',
							'clear_on_false' => true,
							'parameter' => ['user' => $user],
						],
						[
							'user_edit_button',
							'make_section' => true,
							'admin' => true,
							'parameter' => ['user' => $user],
						],
					]
				)
				->withRight(
					[
						[
							'user_info',
							'make_section' => true,
							'section_header' => 'User&apos;s Informaion',
							'parameter' => ['user' => $user],
						],
						[
							'user_addresses',
							'make_section' => true,
							'section_header' => 'Your Addresses',
							'parameter' => ['user' => $user],
							'view_of_self' => true,
						],
						['user_news_for_user'],
						['inter_user_news', 'logged_in' => true, 'view_of_self' => false],
						['send_private_message', 'logged_in' => true, 'view_of_self' => false],
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