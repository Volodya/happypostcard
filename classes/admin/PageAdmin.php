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
							'queue' => [
								[
									'latestpostcards_interuser',
									'logged_in' => true,
									'make_section'=> true,
									'section_header' => 'Latest exchanges with this user',
									'clear_on_false' => true
								],
								['latestpostcards'],
							]
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