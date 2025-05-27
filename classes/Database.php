<?php
// Singleton
class Database
{
	private PDO $db;
	
	private function __construct($db)
	{
		$this->db = $db;
	}
	public function __destruct()
	{
	}
	
	private static $instance;
	
	public static function getInstance() : PDO
	{
		if(self::$instance == null)
		{
			throw new Exception('Database has not been initialised');
		}
		return self::$instance;
	}
	
	public static function init(Config $config) : void
	{
		$dbtype = $config->getProperty('dbtype');
		if(!in_array($dbtype, PDO::getAvailableDrivers()))
		{
			throw new Exception("Database {$dbtype} is not supported by PHP.");
		}
		switch($dbtype)
		{
			case 'sqlite':
				$rootdir = $config->getProperty('rootdir');
				$dbfile = $config->getProperty('dbfile');
				self::$instance = new PDO("sqlite:{$rootdir}/{$dbfile}");
				return;
			default:
				
		}
		throw new Exception('Unknown database type');
	}
}