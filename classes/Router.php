<?php

class Router
{
	private array $routes;
	
	public function __construct(string $routesFile)
	{
		$this->routes = include($routesFile);
	}
	
	public function getPermissionsRequired(Request &$request) : Protector
	{
		if(isset($this->routes[$request->getPageType()]))
		{
			$route = $this->routes[$request->getPageType()];
			
			/*Protector */$protector = new ProtectorAllowAll($request);
			foreach($route['permissions required'] as $required)
			{
				$protectorName = ('ProtectorRequire'.ucfirst($required));
				//echo "<!-- {$protectorName} -->";
				$protector = new $protectorName($protector); // decorator
			}
			return $protector;
		}
		else
		{
			return new ProtectorDenyUrl();
		}
	}
	
	public function getPage(string $route, array $additionalUrl) : Page
	{
		$route = $this->routes[$route];
		
		$pageClass = $route['page'];
		if($route['subpath_allowed'])
		{
			return new $pageClass($additionalUrl);
		}
		else
		{
			return new $pageClass();
		}
	}
	
	public function getPerformers(string $route) : array // [Performer]
	{
		$performers = [];
		if(isset($this->routes[$route]['performers']) and is_array($this->routes[$route]['performers']))
		{
			foreach($this->routes[$route]['performers'] as $performerInfo)
			{
				$perf = new $performerInfo['class']();
				if(isset($performerInfo['params']) and is_array($performerInfo['params']))
				{
					$perf = $perf->addAdditionalParameters($performerInfo['params']);
				}
				$performers[] = $perf;
			}
		}
		else // fallback
		{
			$performerClass = 
				isset($this->routes[$route]['performer'])
					? $this->routes[$route]['performer']
					: 'PerformerPassthrough';
			$performers[] = new $performerClass();
		}
		
		return $performers;
	}
	
	public function response(Request $request, Config $config, Response $response) : Response
	{
		$route = $request->getPageType();
		if(!isset($this->routes[$route]))
		{
			$page = $this->getPage('/home', []);
		}
		else
		{
			$page = @$this->getPage($route, $request->getPageAdditionalUrl(FILTER_SANITIZE_STRING));
		}
		$response = $response->withPage($page);
		return $response;
	}
}