<?php

class AppStateReadyToRoute
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
	
	public function route() : AppStateReadyToProtect
	{
		$response = $this->router->response($this->request, $this->config, $this->response);
		
		return new AppStateReadyToProtect($this->request, $this->config, $this->router, $response);
	}
}