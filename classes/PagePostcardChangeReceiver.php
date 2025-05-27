<?php

class PagePostcardChangeReceiver extends Page_Abstract
{
	public function __construct(array $additionalUrl)
	{
		$cardCode = $additionalUrl[0];
		
		$this->templated =
 			(new Template('page', ['card_code' => $cardCode, 'additional_title' => $cardCode]))
				->withLeft(
					[
						['gethelp'],
						['account_stats', 'logged_in' => true],
					]
				)
				->withRight(
					[
						['changereceiver_warning'],
						['card_information'],
						['changereceiver'],
					]
				)
				->withBottom(
					[
						['latestpostcards'],
					]
				);
	}
}