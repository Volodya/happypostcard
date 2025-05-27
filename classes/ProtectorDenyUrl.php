<?php

class ProtectorDenyUrl implements Protector
{
	public function __construct()
	{}
	
	public function protect(Request $request, Response $response) : array // list(Request, Response)
	{
		$response = $response
			->withErrorMessage('404 URL does not exist: '.htmlentities(urldecode($request->getPageType())))
			->withPage( (new PageRedirector())->withRedirectTo('/home') );
		return [$request, $response];
	}
}