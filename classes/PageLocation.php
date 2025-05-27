<?php

class PageLocation extends Page_Abstract
{
	public function __construct(array $additionalUrl)
	{
		$locationCode = $additionalUrl[0];
		$this->templated =
 			(new Template('page', ['location_code' => $locationCode, 'additional_title' => 'Location:'.$locationCode]))
				->withLeft(
					[
						['account_stats', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['location_info'],
						['location_stats'],
					]
				)
				->withBottom(
					[
						[
							'queue' => [
								[
									'latestpostcards_location',
									'make_section'=> true,
									'section_header' => 'Latest exchanges to/from this location',
									'clear_on_false' => true
								],
								['latestpostcards'],
							]
						]
					]
				);
	}
}