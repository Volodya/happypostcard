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
						[
							'text',
							'make_section' => true,
							'section_header' => 'Menu',
							'parameter' => "
								<form method='get' action='/admin_travelling'>
									<button>List of All Travelling Postcards</button>
								</form>
								<form method='get' action='/admin_sql'>
									<button>Direct SQL access</button>
								</form>
							",
						]
					]
				)
				->withBottom(
					[
						[
							['latestpostcards'],
						]
					]
				);
	}
}