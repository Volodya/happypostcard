<?php

abstract class Page_AbstractWithPerformerResults extends Page_Abstract
{
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