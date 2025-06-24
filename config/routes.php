<?php

return [
	'/home' => [
		'permissions required' => [],
		'page' => 'PageHome',
		'subpath_allowed' => false,
	],
	'/root' => [
		'permissions required' => ['v010dya'],
		'page' => 'PageSuperadmin',
		'subpath_allowed' => false,
	],
	'/admin' => [	
		'permissions required' => ['admin'],
		'performers' => [],
		'page' => 'PageAdmin',
		'subpath_allowed' => false,
	],
	'/admin_travelling' => [	
		'permissions required' => ['admin'],
		'performers' => ['PerformerListAllTravellingPostcards'],
		'page' => 'PageAdminTravelling',
		'subpath_allowed' => true,
	],
	'/mod' => [
		'permissions required' => ['moderator'],
		'page' => 'PageModeration',
		'subpath_allowed' => false,
	],
	'/news' => [
		'permissions required' => [],
		'page' => 'PageNews',
		'subpath_allowed' => false,
	],
	'/faq' => [
		'permissions required' => [],
		'page' => 'PageFAQ',
		'subpath_allowed' => false,
	],
	'/help' => [
		'permissions required' => [],
		'page' => 'PageHelp',
		'subpath_allowed' => false,
	],
	'/statistics' => [
		'permissions required' => [],
		'page' => 'PageStatistics',
		'subpath_allowed' => false,
	],
	'/location' => [
		'permissions required' => [],
		'page' => 'PageLocation',
		'subpath_allowed' => true,
	],
	'/card' => [
		'permissions required' => [],
		'page' => 'PagePostcard',
		'subpath_allowed' => true,
	],
	'/image' => [
		'permissions required' => [],
		'page' => 'PageImage',
		'subpath_allowed' => true,
	],
	'/send' => [
		'permissions required' => ['user'],
		'page' => 'PageSendPostcard',
		'subpath_allowed' => false,
	],
	'/birthday' => [
		'permissions required' => ['user'],
		'page' => 'PageSendBirthdayCard',
		'subpath_allowed' => false,
	],
	'/changereceiver' => [
		'permissions required' => ['user'],
		'page' => 'PagePostcardChangeReceiver',
		'subpath_allowed' => true,
	],
	'/performselectaddress' => [
		'permissions required' => ['user'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerSelectAddress',
		'subpath_allowed' => false,
	],
	'/performreselectaddress' => [
		'permissions required' => ['user'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerReselectAddress',
		'subpath_allowed' => false,
	],
	'/performupload' => [
		'permissions required' => ['user'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerUpload',
		'subpath_allowed' => false,
	],
	'/performswapimageposition' => [
		'permissions required' => ['user'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerSwapImagePosition',
		'subpath_allowed' => false,
	],
	'/performunlinkimage' => [
		'permissions required' => ['user'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerUnlinkImage',
		'subpath_allowed' => false,
	],
	'/performgethelp' => [
		'permissions required' => [],
		'page' => 'PageRedirector',
		'performer' => 'PerformerGetHelp',
		'subpath_allowed' => false,
	],
	'/performsendprivatemessage' => [
		'permissions required' => ['user'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerSendPrivateMessage',
		'subpath_allowed' => false,
	],
	'/receive' => [
		'permissions required' => ['user'],
		'page' => 'PageReceivePostcard',
		'subpath_allowed' => false,
	],
	'/performreceivepostcard' => [
		'permissions required' => ['user'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerReceivePostcard',
		'subpath_allowed' => false,
	],
	'/discuss' => [
		'permissions required' => ['user'],
		'page' => 'PageDiscuss',
		'subpath_allowed' => false,
	],
	'/account' => [
		'permissions required' => ['user'],
		'page' => 'PageAccount',
		'subpath_allowed' => false,
	],
	'/performpasswordchange' => [
		'permissions required' => ['user'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerPasswordChange',
		'subpath_allowed' => false,
	],
	'/user' => [
		'permissions required' => [],
		'page' => 'PageUser',
		'subpath_allowed' => true,
	],
	'/useredit' => [
		'permissions required' => ['user'],
		'page' => 'PageUserEdit',
		'subpath_allowed' => true,
	],
	'/userphotos' => [
		'permissions required' => [],
		'page' => 'PageUserPhotos',
		'subpath_allowed' => true,
	],
	'/wpd_cards' => [
		'permissions required' => [],
		'page' => 'PageWPDCards',
		'subpath_allowed' => false,
	],
	'/performenable' => [
		'permissions required' => ['user'],
		'performer' => 'PerformerProfileEnable',
		'page' => 'PageRedirector',
		'subpath_allowed' => false,
	],
	'/perform_useredittravelling' => [
		'permissions required' => ['user'],
		'performer' => 'PerformerProfileEditTravelling',
		'page' => 'PageRedirector',
		'subpath_allowed' => false,
	],
	'/territory' => [
		'permissions required' => [],
		'page' => 'PageTerritory',
		'subpath_allowed' => true,
	],
	'/login' => [
		'permissions required' => [],
		'page' => 'PageLoginUser',
		'subpath_allowed' => false,
	],
	'/performlogin' => [
		'permissions required' => [],
		'page' => 'PageRedirector',
		'performer' => 'PerformerLoginUser',
		'subpath_allowed' => false,
	],
	'/performlogout' => [
		'permissions required' => [],
		'page' => 'PageRedirector',
		'performer' => 'PerformerLogout',
		'subpath_allowed' => false,
	],
	'/recoverpass' => [
		'permissions required' => [],
		'page' => 'PageRecoverPassword',
		'subpath_allowed' => false,
	],
	'/performrecoverpass' => [
		'permissions required' => [],
		'page' => 'PageRedirector',
		'performer' => 'PerformerRecoverPassword',
		'subpath_allowed' => false,
	],
	'/register' => [
		'permissions required' => [],
		'performers' => ['PerformerGuessUserLocation'],
		'page' => 'PageRegister',
		'subpath_allowed' => false,
	],
	'/performregistration' => [
		'permissions required' => [],
		'page' => 'PageRedirector',
		'performer' => 'PerformerRegisterUser',
		'subpath_allowed' => false,
	],
	'/performprofileedit' => [
		'permissions required' => ['user'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerProfileEdit',
		'subpath_allowed' => false,
	],
	'/performapproveaddress' => [
		'permissions required' => ['v010dya'],
		'page' => 'PageRedirector',
		'performer' => 'PerformerApproveAddress',
		'subpath_allowed' => false,
	],
	'/travelling' => [
		'permissions required' => ['user'],
		'page' => 'PageTravelling',
		'subpath_allowed' => false,
	],
	'/sent' => [
		'permissions required' => [],
		'page' => 'PageCompletedSent',
		'subpath_allowed' => true,
	],
	'/received' => [
		'permissions required' => [],
		'page' => 'PageCompletedReceived',
		'subpath_allowed' => true,
	],
	'/users' => [
		'permissions required' => [],
		'page' => 'PageListOfUsers',
		'subpath_allowed' => false,
	],
	
	'/sitemaptext' => [
		'permissions required' => [],
		'page' => 'PageSitemapText',
		'subpath_allowed' => false,
	],
	
	'/api_imagecount' => [
		'permissions required' => ['user'],
		'page' => 'JsonImageCounter',
		'subpath_allowed' => true,
	],
	'/api_linkimage' => [
		'permissions required' => ['user'],
		'performer' => 'PerformerLinkImageToCard',
		'page' => 'JsonResult',
		'subpath_allowed' => false,
	],
];