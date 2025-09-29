<?php

class PageSuperadmin extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'SUPERADMIN']))
				->withLeft(
					[
						[
							'admin_section',
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