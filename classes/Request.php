<?php

class Request
{
	private array $server;
	private array $get;
	private array $post;
	private array $files;
	private array $cookies;
	private array $session;
	private string $pageRequested;
	private array $pageRequestedParts;
	private string $pageDomain;
	
	private ?UserExisting $loggedInUser;
	
	public function __construct(array $server, array $get,  array $post, array $files, array $cookies, array $session)
	{
		$this->server = $server;
		$this->get = $get;
		$this->post = $post;
		$this->files = $this->rewriteFiles($files);
		$this->cookies = $cookies;
		$this->session = $session;
		$this->pageRequested = $server['REQUEST_URI'];
		$this->pageRequestedParts = array_map('urldecode', explode('/', $server['REQUEST_URI']));
		$this->pageDomain = array_shift($this->pageRequestedParts);
		
		$this->loggedInUser = null;
	}
	
	public function allSetPOST(array $keys) : bool
	{
		$result = true;
		foreach($keys as $key)
		{
			if(!isset($this->post[$key]) || empty($this->post[$key]))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}
	public function allSetGET(array $keys) : bool
	{
		$result = true;
		foreach($keys as $key)
		{
			if(!isset($this->get[$key]) || empty($this->get[$key]))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}
	
	public function getSERVER() : array
	{
		return array_merge([], $this->server);
	}
	// $keys =
	//         [
	//           'name' =>
	//             [
	//                'default' => '',
	//                'filter' => (SEE filter_var),
	//                'custom_filter' => (see CustomFilters class),
	//                'filter_options' => 0
	//             ],
	//           'name2' => [...]
	//         ]
	private function getREQUEST(array $request, array $keys) : array
	{
		$result = [];
		foreach($keys as $key=>$options)
		{
			$isArray = isset($options['isArray']) ? $options['isArray'] : false;
			
			$useRegularFilter = !isset($options['custom_filter']);
			if($useRegularFilter)
			{
				$filter = isset($options['filter']) ? $options['filter'] : FILTER_DEFAULT;
				$filter_options = isset($options['filter_options']) ? $options['filter_options'] : 0;
			}
			else
			{
				if(method_exists('CustomFilters', $options['custom_filter']))
				{
					$filter = $options['custom_filter'];
				}
				else
				{
					throw new Exception("custom filter doesn't exist");
				}
			}
			$default = isset($options['default']) ? $options['default'] : '';
			
			if(!$isArray)
			{
				if($useRegularFilter)
				{
					$result[$key] = isset($request[$key])
						? filter_var($request[$key], $filter, $filter_options)
						: $default;
				}
				else
				{
					$result[$key] = isset($request[$key])
						? CustomFilters::$filter($request[$key])
						: $default;
				}
			}
			else
			{
				$ar = (isset($request[$key]) and is_array($request[$key])) ? $request[$key] : [];
				$result[$key] = [];
				
				foreach($ar as $val)
				{
					if($useRegularFilter)
					{
						$result[$key][] = filter_var($val, $filter, $filter_options);
					}
					else
					{
						$result[$key][] = CustomFilters::$filter($val);
					}
				}
			}
		}
		return $result;
	}
	public function getGET(array $keys) : array
	{
		return $this->getREQUEST($this->get, $keys);
	}
	public function getPOST(array $keys) : array
	{
		return $this->getREQUEST($this->post, $keys);
	}
	public function getFILES() : array
	{
		return array_merge([], $this->files);
	}
	public function getCOOKIES() : array
	{
		return array_merge([], $this->cookies);
	}
	public function getSESSION() : array
	{
		return array_merge([], $this->session);
	}
	
	// https://stackoverflow.com/a/38852532/2893496
	public function getClientIp() : string
	{
		$ipaddress = 'UNKNOWN';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		}
		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else if (isset($_SERVER['HTTP_X_FORWARDED']))
		{
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		}
		else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
		{
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		}
		else if (isset($_SERVER['HTTP_FORWARDED']))
		{
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		}
		else if (isset($_SERVER['REMOTE_ADDR']))
		{
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		}

		return $ipaddress;
	}
	
	public function isLoggedIn() : bool
	{
		if(isset($this->session['login_id']))
		{
			return true;
		}
		else
		{
			if(isset($this->cookies['persistent']))
			{
				try
				{
					$this->loggedInUser = UserExisting::constructByPersistentLogin($this->cookies['persistent']);
					$this->loggedInUser->updateLoggedInDate();
					
					$_SESSION['login_id'] = $this->session['login_id'] = $this->loggedInUser->getId(); // redo!
					
					return true;
				}
				catch(Exception $e)
				{
					User::removeSecret($this->cookies['persistent']);
					//setcookie('persistent', '', -1, '/'); // redo! unset cookie
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
	public function getLoggedInUser() : UserExisting
	{
		if($this->loggedInUser==null)
		{
			$this->loggedInUser = UserExisting::constructById($this->session['login_id']);
		}
		return $this->loggedInUser;
	}
	
	public function getUserNotices() : array
	{
		if(isset($this->session['notices']) && is_array($this->session['notices']))
		{
			return array_merge([], $this->session['notices']);
		}
		else
		{
			return [];
		}
	}
	public function getUserErrors() : array
	{
		if(isset($this->session['errors']) && is_array($this->session['errors']))
		{
			return array_merge([], $this->session['errors']);
		}
		else
		{
			return [];
		}
	}
	
	public function getPageType($filter = FILTER_UNSAFE_RAW) : string
	{
		return '/'.rtrim(filter_var($this->pageRequestedParts[0], $filter), '?');
	}
	public function getPageAdditionalUrl($filter = FILTER_UNSAFE_RAW) : array // returns array of strings www.example.com/1/2/3 = ['2', '3']
	{
		$result = $this->pageRequestedParts;
		array_shift($result);
		foreach($result as &$val)
		{
			$val = filter_var($val, $filter);
		} unset($val);
		
		return $result;
	}
	
	public function blockRequest() : Request
	{
		$new = clone $this;
		$new->get = [];
		$new->post = [];
		$new->files = [];
		$new->pageRequested = '/home';
		$new->pageRequestedParts = ['home'];
		return $new;
	}
	
	/* PRIVATE FUNCTIONS */
	
	private function rewriteFiles(array $oldFiles) : array
	{
		$result = array();
		foreach($oldFiles as $name => $fileArray)
		{
			if (is_array($fileArray['name']))
			{
				foreach ($fileArray as $attrib => $list)
				{
					foreach ($list as $index => $value)
					{
						$result[$name][$index][$attrib]=$value;
					}
				}
			}
			else
			{
				$result[$name][] = $fileArray;
			}
		}
		return $result;
	}
}