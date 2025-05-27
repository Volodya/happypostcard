<?php

class AppStateReadyToMakePage
{
	private Request $request;
	private Config $config;
	private Router $router;
	private Response $response;
	
	public function __construct(Request $request, Config $config, Router $router, Response $response)
	{
		$this->request = $request;
		$this->config = $config;
		$this->router = $router;
		$this->response = $response;
	}
	
	public function makePage() : AppStateReadyToSendResults
	{
		$response = $this->response;
		
		$page = $response->getPage();
		
		if($this->request->isLoggedIn())
		{
			$user = $this->request->getLoggedInUser();
			$page = $page->withUser($user);
		}
		
		$page = $page->withExtraSiteNotices(
			$this->config->getPropertyOrDefault('sitenotices', [])
		);
		
		
		$page = $page->withExtraUserNotices(
			$this->request->getUserNotices()
		);
		$page = $page->withExtraErrors(
			$this->request->getUserErrors()
		);
		
		$response = $response->withPage($page);
		
		return new AppStateReadyToSendResults($this->request, $this->config, $this->router, $response);
	}
}