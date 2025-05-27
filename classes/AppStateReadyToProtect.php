<?php

class AppStateReadyToProtect
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
	
	public function protect() : AppStateReadyToPerformTasks
	{
		$protector = $this->router->getPermissionsRequired($this->request);
		
		list($request, $response) = $protector->protect($this->request, $this->response);
		
		return new AppStateReadyToPerformTasks($request, $this->config, $this->router, $response);
	}
}