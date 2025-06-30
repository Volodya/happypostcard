<?php

class PageListOfUsers extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page', []))
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
						[
							'display_list_of_users',
							'make_section' => true,
							'section_header' => 'Recently active users',
							'parameter' => [
								'performer_results_key' => 'recent_users',
								'list_type' => 'p',
							],
						],
						[
							'display_list_of_users',
							'make_section' => true,
							'section_header' => 'Recently registered users',
							'parameter' => [
								'performer_results_key' => 'recently_joined_users',
								'list_type' => 'p',
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