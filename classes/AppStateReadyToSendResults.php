<?php

class AppStateReadyToSendResults
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
	
	public function sendResult() : void
	{
		$this->response->send();
		return; // new AppStateReadyTo-($this->request, $this->config, $this->router, $this->response);
	}
}