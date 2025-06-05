<?php

class AppStateReadyToPerformTasks
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
	
	public function performTasks() : AppStateReadyToMakePage
	{
		$pageRequested = $this->request->getPageType();
		$performers = $this->router->getPerformers($pageRequested);
		
		$response = $this->response;
		$performerResults = [];
		foreach($performers as $performer)
		{
			$response = $performer->perform($this->request, $response, $this->config);
			$performerResults = array_merge($performerResults, $performer->getResult());
		}
		$response = $response->withPerformerResults($performerResults);
		
		return new AppStateReadyToMakePage($this->request, $this->config, $this->router, $response);
	}
}