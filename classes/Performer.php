<?php

interface Performer
{
	public function addAdditionalParameters(array $params) : void;
	public function perform(Request $request, Response $response, Config $config) : Response;
	public function getResult() : array;
	//public function getErrorState() : ErrorState;
}