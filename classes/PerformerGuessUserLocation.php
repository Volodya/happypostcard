<?php

class PerformerGuessUserLocation extends Performer_Abstract
{
	private array $result;
	public function __construct()
	{
		$this->result = ['location'=>['id'=>4822, 'code'=>'SOL3', 'name'=>'Earth']];
	}
	public function addAdditionalParameters(array $params) : void
	{}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		$ip = $request->getClientIp();
		if(preg_match('/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/', $ip, $matches) != 1) return $response;
		$ip = $matches[0];
		$json = file_get_contents("http://ip-api.com/json/{$ip}");
		$json = json_decode($json, true);
		
		if($json == null) return $response;
		if(!isset($json['status']) or $json['status'] != 'success') return $response;
		
		if(isset($json['countryCode']) and isset($json['region']))
		{
			try
			{
				$location = Location::getLocationByUN_sub($json['countryCode'], $json['region']);
				$this->result=['location'=>$location];
				return $response;
			}
			catch(Exception $e)
			{}
		}
		if(isset($json['countryCode']))
		{
			try
			{
				$location = Location::getLocationByISO3166_1_a2($json['countryCode']);
				$this->result=['location'=>$location];
				return $response;
			}
			catch(Exception $e)
			{}
		}
		
		return $response;
	}
	public function getResult() : array
	{
		return $this->result;
	}
}