<?php

class PageDirectSql extends Page_AbstractWithPerformerResults
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
							'section_header' => 'Direct SQL',
							'parameter' => [
								'action' => '/performdirectsql',
								'inputs' => [
									[
										'tag' => 'textarea',
										'name' => 'sql',
										'label' => 'SQL',
										
										'cols' => 120,
										'rows' => 15,
									],
									[
										'tag' => 'submit',
										'inner_text' => 'Run',
									],
								],
							],
						],
						[
							'display_table',
							'make_section' => true,
							'section_header' => 'Result',
							'parameter' =>
								[
									'show_sql' => true
								]
						],
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