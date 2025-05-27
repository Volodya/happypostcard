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
						['account_stats', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['image', 'make_section' => true, 'section_header' => 'Image'],
						['image_information', 'make_section' => true, 'section_header' => 'Information'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}