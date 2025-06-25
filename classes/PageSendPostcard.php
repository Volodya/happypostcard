<?php

class PageSendPostcard extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page'))
				->withLeft(
					[
						['gethelp'],
						[
							'user_statistics',
							'make_section' => true,
							'logged_in' => true,
						],
						[
							'FAQ',
							'make_section' => true,
							'section_header' => 'From the FAQ',
							'parameter' => [
								'questions'=>[3, 4, 5, 6, 8, 17],
								'type'=>'random',
							],
						],
					]
				)
				->withRight(
					[
						['sendpostcard_note'],
						['sendpostcard'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}