<?php

abstract class Performer_Abstract implements Performer
{
	protected function abandon(Response $response, string $error) : Response
	{
		return $response
			->withErrorMessage($error);
	}
	
	public function addAdditionalParameters(array $params) : void
	{
	}
	public abstract function perform(Request $request, Response $response, Config $config) : Response;
	public function getResult() : array
	{
		return [];
	}
}