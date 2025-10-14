<?php

class PerformerRegisterUser extends Performer_Abstract
{
	public function sendEmail(User $user) : void
	{
		$userEmail = $user->getEmail();
		$userLocation = $user->getHomeLocation();
		
		$email = new EMail();
		
		$email = $email
			->withSubject('〠 You have been registered on Happy Postcard')
			->withExtraTo($userEmail['email'], $userEmail['polite_name'])
			->withExtraNoscriptBody(
				"Good time of the day, {$userEmail['polite_name']}!"
				."\r\n"."\r\n"
				."Thanks a lot for joining the community on Happy Postcard. "
				."You are currently representing {$senderLocation['name']}."
				."\r\n"."\r\n"
				."Please login to the site, set your postal address and you can start sending out postcards. "
				."In order for other people to know what cards best suits you, you can also fill out your profile."
			);
		
		$email->mail();
	}
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
		
		$user = User::generateUser($config, $post['login'], $post['password'], $post['email'], $post['home_location']);
		
		$page = (new PageRedirector())->withRedirectTo('/login');
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('User has been created. Please login.');
		
		$this->sendEmail($user);
		
		return $response;
	}
}