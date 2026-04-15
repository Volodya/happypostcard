<?php

class PageImage extends Page_Abstract
{
	public function __construct(array $additionalUrl)
	{
		$hash = $additionalUrl[0];
		
		$this->templated =
 			(new Template('page', ['hash' => $hash, 'additional_title' => 'Image']))
				->withLeft(
					[
						['login', 'logged_in' => false],
						[
							'user_statistics',
							'make_section' => true,
							'logged_in' => true,
						],
					]
				)
				->withRight(
					[
						['image', 'make_section' => true, 'section_header' => 'Image'],
						['image_information', 'make_section' => true, 'section_header' => 'Information'],
						[
							'form',
							'admin' => true,
							'make_section' => true,
							'section_header' => 'Rotate image',
							'parameter' => [
								'action' => '/performrotateimage',
								'inputs' => [
									[
										'tag' => 'hidden',
										'name' => 'hash',
										'value' => $hash
									],
									[
										'tag' => 'hidden',
										'name' => 'type',
										'value' => 'unknown',
									],
									[
										'tag' => 'submit',
										'name' => 'rotate',
										'value' => '0',
										'inner_text' => '0',
									],
									[
										'tag' => 'submit',
										'name' => 'rotate',
										'value' => '90',
										'inner_text' => '90',
									],
									[
										'tag' => 'submit',
										'name' => 'rotate',
										'value' => '180',
										'inner_text' => '180',
									],
									[
										'tag' => 'submit',
										'name' => 'rotate',
										'value' => '270',
										'inner_text' => '270',
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