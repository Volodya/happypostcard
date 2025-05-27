<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

// autoloading classes
class ClassLoader
{
	private const DIR = 'classes';
	private array $classes;
	private static function getClassesRecursiveMap(string $dir) : array
	{
		$result = [];
		
		// remove / from the end
		while (substr($dir,-1,1)==DIRECTORY_SEPARATOR) $path=substr($dir,0,-1);
		
		$dirHandle = opendir($dir);
		while(($file = readdir($dirHandle)) !== false)
		{
			if($file == '.' || $file == '..') continue;
			if(is_dir($dir.DIRECTORY_SEPARATOR.$file))
			{
				$result = array_merge($result, self::getClassesRecursiveMap($dir.DIRECTORY_SEPARATOR.$file));
			}
			elseif(substr($file, -4, 4) === '.php')
			{
				$result[substr($file, 0, -4)] = $dir.'/'.$file;
			}
		}
		return $result;
	}
	public function __construct()
	{
		$this->classes = ClassLoader::getClassesRecursiveMap(self::DIR);
	}
	public function load(string $class_name) : void
	{
		if(array_key_exists($class_name, $this->classes))
		{
			require_once($this->classes[$class_name]);
		}
	}
}
$CLASS_LOADER = new ClassLoader();
spl_autoload_register(function (string $class_name) use (&$CLASS_LOADER) {
	$CLASS_LOADER->load($class_name);
//    require_once('classes/' . $class_name . '.php');
});
set_include_path($_SERVER['DOCUMENT_ROOT']);

// str_contains
if(PHP_VERSION_ID < 80000)
{
	function str_contains(string $haystack, string $needle): bool
	{
		return $needle !== '' && strpos($haystack, $needle) !== false;
	}
}

session_start();

(new AppState())
->initialise(
	new Request(
		isset($_SERVER)  ? $_SERVER  : [],
		isset($_GET)     ? $_GET     : [],
		isset($_POST)    ? $_POST    : [],
		isset($_FILES)   ? $_FILES   : [],
		isset($_COOKIE)  ? $_COOKIE  : [],
		isset($_SESSION) ? $_SESSION : [],
	),
	new Config('config/config.php'),
	new Router('config/routes.php'),
	new Response())
->route()
->protect()
->performTasks()
->makePage()
->sendResult()
;
$_SESSION['last_timestamp'] = time();