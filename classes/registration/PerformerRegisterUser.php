<?php

class PerformerRegisterUser extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['login', 'password', 'password2']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'login'=>[
				'custom_filter' => 'FILTER_SANITIZE_LOGIN'
			],
			'password'=>[
				'filter' => FILTER_UNSAFE_RAW
			],
			'password2'=>[
				'filter' => FILTER_UNSAFE_RAW
			],
			'home_location' => [
				'filter' => FILTER_UNSAFE_RAW, // will check in database
			],
			'email'=>[
				'filter'=>FILTER_VALIDATE_EMAIL
			]]);
		
		if($post['password'] != $post['password2'])
		{
			return $this->abandon($response, 'passwords do not match');
		}
		
		if(User::exists($post['login']))
		{
			return $this->abandon($response, 'user already exists');
		}
		if(User::existsEmail($post['email']))
		{
			return $this->abandon($response, 'user with that email already exists');
		}
		
		$user = User::generateUser($config, $post['login'], $post['password'], $post['email']);
		$user->updateHomeLocation(Location::getIdByCode($post['home_location']));
		
		$page = (new PageRedirector())->withRedirectTo('/login');
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('User has been created. Please login.');
		
		return $response;
	}
}