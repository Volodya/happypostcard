<?php

class PageRegister extends Page_Abstract
{
	public function __construct()
	{
		$this->templated =
 			(new Template('page'))
				->withLeft(
					[
						['gethelp'],
					]
				)
				->withRight(
					[
						['registration'],
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