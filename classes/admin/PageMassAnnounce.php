<?php

class PageMassAnnounce extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', ['additional_title' => 'Mass Announce']))
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
							'section_header' => 'Mass announcement',
							'parameter' => [
								'action' => '/performmassannounce',
								'inputs' => [
									[
										'tag' => 'textarea',
										'name' => 'sql',
										'label' => 'SQL',
									],
									[
										'tag' => 'text',
										'name' => 'subject',
										'label' => 'Subject',
									],
									[
										'tag' => 'textarea',
										'name' => 'body',
										'label' => 'Body',
									],
									[
										'tag' => 'submit',
										'inner_text' => 'Send',
									],
								],
							],
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