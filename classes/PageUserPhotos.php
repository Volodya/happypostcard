<?php

class PageUserPhotos extends Page_Abstract
{
	public function __construct(array $additionalUrl)
	{
		$user = User::constructByLogin($additionalUrl[0]);
		$this->templated =
 			(new Template('page', ['user' => $user, 'additional_title' => $user->getLogin().'&apos;s photoalbum']))
				->withLeft(
					[
						['account_stats_for_user'],
						//['user_main_image', 'make_section' => true]
					]
				)
				->withRight(
					[
						['user_photographs', 'make_section' => true],
						[
							'user_info_add_photo',
							'logged_in' => true, 'view_of_self' => true,
							'make_section' => true, 'section_header' => 'Upload photographs',
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