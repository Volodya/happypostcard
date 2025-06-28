<?php

class CustomFilters
{
	public static function FILTER_SANITIZE_NOSCRIPT(string $data): string
	{
		$unsafe= array('<', '>');
		$safe  = array('〈', '〉');
		
		return str_replace($unsafe, $safe, $data);
	}
	
	public static function FILTER_SANITIZE_DATE(string $data) : string
	{
		try
		{
			return DateTimeImmutable::createFromFormat('Y-m-d', $data)->format('Y-m-d');
		}
		catch (Throwable $e)
		{
			return '';
		}
	}
	public static function FILTER_SANITIZE_ALPHANUMERIC(string $data) : string
	{
		return preg_replace("/[^a-zA-Z0-9]+/", "", $data);
	}
	public static function FILTER_SANITIZE_LOGIN(string $data) : string
	{
		$data = iconv("UTF-8", "UTF-8//IGNORE", $data); // drop all non utf-8 characters
		$data = filter_var($data, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
		
		$data = trim($data);
		
		$unsafe= array('<', '>', '/', '\\', ' ', '.', "'", '"', '@');
		$safe  = array('〈', '〉', '╱', '╲',  '_', '․', '’', '”', 'ⓐ');
		
		$data = str_replace($unsafe, $safe, $data);
		
		if(str_contains($data, '.'))
		{
			echo 'Somehing horrible is happening, that makes no sense! '.base64_encode($data);
			die();
		}
		
		return $data;
	}
}