<?php

class PageAdmin extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'SUPERADMIN']))
				->withLeft(
					[
						[
							'text',
							'make_section' => true,
							'section_header' => 'ADMIN',
							'parameter' => 'You are now in the admin section',
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
						//['user_photographs', 'make_section' => true],
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