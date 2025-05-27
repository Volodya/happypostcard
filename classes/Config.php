<?php

class Config
{
	private array $properties;
	
	public function __construct(string $configPath)
	{
		if(empty($configPath))
		{
			$this->properties = [];
		}
		else
		{
			$this->properties = include($configPath);
		}
	}
	
	public function getPropertyOrDefault(string $name, $defaultValue)
	{
		if(!isset($this->properties[$name]))
		{
			return $defaultValue;
		}
		return $this->properties[$name];
	}
	
	public function getPropertyOrThrow(string $name, throwable $exception/* = new Exception('Property is not defined')*/)
	{
		if(!isset($this->properties[$name]))
		{
			throw $exception;
		}
		return $this->properties[$name];
	}
	
	public function getProperty(string $name)
	{
		return $this->getPropertyOrThrow($name, new Exception("Property '{$name}' is not defined in config."));
	}
	
	private static Config $instance;
	public static function makePublic(Config $config) : void
	{
		self::$instance = $config;
	}
	public static function getInstance() : Config
	{
		return self::$instance;
	}
}