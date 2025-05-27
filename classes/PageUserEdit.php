<?php

class PageUserEdit extends Page_Abstract
{
	public function __construct(array $additionalUrl)
	{
		$user = User::constructByLogin($additionalUrl[0]);
		$this->templated =
 			(new Template('page', ['user' => $user, 'additional_title' => $user->getLogin()]))
				->withLeft(
					[
						['account_stats_for_user'],
					]
				)
				->withRight(
					[
						['user_info_edit', 'logged_in' => true/*, 'view_of_self' => true*/], // will check permission in complexwidgets
						[
							'user_info_edit_travelling',
							'logged_in' => true, 'view_of_self' => true,
							'make_section' => true, 'section_header' => 'Travelling mode',
						],
						[
							'user_disable',
							'logged_in' => true, 'view_of_self' => true,
							'make_section' => true//, 'section_header' => 'Disable/Reenable',
						],
						[
							'user_info_add_photo',
							'logged_in' => true, 'view_of_self' => true,
							'make_section' => true, 'section_header' => 'Upload photographs',
						],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}