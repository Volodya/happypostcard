<?php

class PerformerPassthrough extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		Config::makePublic($config);
		return $response;
	}
}