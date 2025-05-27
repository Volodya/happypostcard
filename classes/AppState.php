<?php

class AppState
{
	public function __construct()
	{}
	
	public function initialise(Request $request, Config $config, Router $router, Response $response) : AppStateReadyToRoute
	{
		Database::init($config);
		return new AppStateReadyToRoute($request, $config, $router, $response);
	}
}