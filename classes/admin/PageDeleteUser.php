<?php

class PageDeleteUser extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'DELETE USER']))
				->withLeft(
					[
						[
							'admin_section',
						],
						[
							'users_waitingapproval',
							'admin' => true,
							'make_section' => true,
							'section_header' => 'Waiting for aproval',
							'clear_on_false' => true,
							'parameter' => [],
						],
					]
				)
				->withRight(
					[
						[
							'form',
							'make_section' => true,
							'section_header' => 'Delete a User by login',
							'parameter' => [
								'action' => '/performdeleteuser',
								'inputs' => [
									[
										'tag' => 'text',
										'name' => 'login',
										'label' => 'login',
									],
									[
										'tag' => 'submit',
										'inner_text' => 'Delete this user',
									],
								],
							],
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