<?php

class PagePostcard extends Page_Abstract
{
	public function __construct(array $additionalUrl)
	{
		$cardCode = $additionalUrl[0];
		
		$this->templated =
 			(new Template('page', ['card_code' => $cardCode, 'additional_title' => $cardCode]))
				->withLeft(
					[
						['login', 'logged_in' => false],
						[
							'user_statistics',
							'make_section' => true,
							'logged_in' => true,
						],
						[
							'user_main_image',
							'make_section' => true,
							'clear_on_false' => true,
						],
					]
				)
				->withRight(
					[
						['card_information'],
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
									'section_header' => 'Latest exchanges with these users',
									'clear_on_false' => true
								],
								['latestpostcards'],
							]
						]
					]
				);
	}
}