<?php

class UserAnonymous extends User
{
	public function __construct()
	{
	}
	public function getId() : string
	{
		throw new Exception('User is not logged in');
	}
	public function getLogin() : string
	{
		return 'Anonymous';
	}
	public function getPreferenceOrDefault(string $key, string $default) : string
	{
		return $default;
	}
}