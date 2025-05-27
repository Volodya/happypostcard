<?php

error_reporting(E_ALL);

return [
	'sitenotices' => [
		[
			'header' => 'Under development',
			'text' => 'The site is still under development!',
		],
	],
	
	'rootdir' => realpath('.'),
	
	'online' => true,
	'superadmin' => 'v010dya',
	
	'dbtype' => 'sqlite',
	'dbfile' => 'data/db.sqlite3',
	
	'passhash_options' => [
		'algorythm' => PASSWORD_DEFAULT,
		'options' => [ 'cost' => 12 ],
	],
];