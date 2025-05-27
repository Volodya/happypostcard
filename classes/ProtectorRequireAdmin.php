<?php

class ProtectorRequireAdmin implements Protector
{
	private Protector $parent;
	
	public function __construct(Protector &$parent)
	{
		$this->parent = $parent;
	}
	
	public function protect(Request $request, Response $response) : array // list(Request, Response)
	{
		if(!$request->isLoggedIn())
		{
			$response = $response->withErrorMessage('Need to login');
			$response = $response->withPage(
				(new PageRedirector())->withRedirectTo('/login')
			);
			$request = $request->blockRequest();
			return [$request, $response];
		}
		$user = $request->getLoggedInUser();
		if($user->getLogin() != 'v010dya') // TODO: allow others
		{
			$response = $response->withErrorMessage('Priviledges insufficient');
			$response = $response->withPage(
				(new PageRedirector())->withRedirectTo('/home')
			);
			$request = $request->blockRequest();
			return [$request, $response];
		}
		return $this->parent->protect($request, $response);
	}
	
	
}