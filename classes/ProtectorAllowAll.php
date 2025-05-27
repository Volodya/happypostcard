<?php

class ProtectorAllowAll implements Protector
{
	public function __construct()
	{
	}
	
	public function protect(Request $request, Response $response) : array // list(Request, Response)
	{
		return [$request, $response];
	}
}