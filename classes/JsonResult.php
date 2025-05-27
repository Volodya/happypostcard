<?php

class JsonResult extends JsonGenerator
{
	private array $performerResult;
	public function __construct()
	{
		$this->performerResult = [];
	}
	
	public function showsPerformerResults() : bool
	{
		return true;
	}
	public function withPerformerResult(array $performerResult)
	{
		$new = clone $this;
		$new->performerResult = $performerResult;
		return $new;
	}
	
	public function toString() : string
	{
		return json_encode($this->performerResult);
	}
}
