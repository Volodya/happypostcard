<?php

class PageAdminTravelling extends Page_Abstract
{
	public function __construct(array $additionalUrl)
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
						[
							'display_list_of_cards',
							'make_section' => true,
							'parameter' => [
								'columns'=> [
									'sender', 'sender_loc', 'sent_at', 'days_travelling', 'postcard_code',
									'receiver', 'receive_loc', 'first_image_hash',
								]
							],
						],
					]
				)
				->withBottom(
					[
						[
						]
					]
				);
	}
	public function showsPerformerResults() : bool
	{
		return true;
	}
	public function withPerformerResults(array $performerResults) : Page
	{
		$new = clone $this;
		$new->templated = $this->templated->withPerformerResults($performerResults);
		return $new;
	}
}