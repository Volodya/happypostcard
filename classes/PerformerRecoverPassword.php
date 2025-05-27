<?php

class PerformerRecoverPassword extends Performer_Abstract
{
	public function sendSecret(UserExisting $user, string $secret) : void
	{
		$userEmail = $user->getEmail();
		
		$email = new EMail();
		
		$email = $email
			->withSubject("Your password recovery secret [Happy Postcard]")
			->withExtraTo($userEmail['email'], $userEmail['polite_name'])
			->withExtraNoscriptBody(
				"Good time of the day, {$userEmail['polite_name']}!"
				."\r\n"."\r\n"
				."Somebody, hopefully you, have requested a recovery of your password. "
				."Password information is stored in such a way, that nobody, not even administrator of the site "
				."can or should know it. Therefore in order to reset your password you must do so yourself."
				."\r\n"."\r\n"
				."Submit this secret string as a step 2 of the recovery process:"
				."\r\n"."\r\n"
				.$secret
			);
		
		$email->mail();
	}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['step']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'step' => [
				'filter' => FILTER_SANITIZE_NUMBER_INT
			]
		]);
		switch(intval($post['step']))
		{
			case 1:
				return $this->performStep1($request, $response, $config);
			case 2:
				return $this->performStep2($request, $response, $config);
		}
	}
	public function performStep1(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['login']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'login'=>[
				'custom_filter' => 'FILTER_SANITIZE_LOGIN'
			],
		]);
		
		try
		{
			$user = UserExisting::constructByLogin($post['login']);
		}
		catch(Exception $e)
		{
			return $this->abandon($response, 'There is no user with such login');
		}
		
		$secret = $user->producePasswordRecoverySecret();
		$this->sendSecret($user, $secret);
		
		$page = (new PageRedirector())->withRedirectTo('/recoverpass');
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('Please check your email and follow step 2');
		
		return $response;
	}
	public function performStep2(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['code', 'password', 'password2']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'code'=>[
				'custom_filter' => 'FILTER_SANITIZE_ALPHANUMERIC'
			],
			'password'=>[
				'filter' => FILTER_UNSAFE_RAW
			],
			'password2'=>[
				'filter' => FILTER_UNSAFE_RAW
			],
		]);
		
		if($post['password'] != $post['password2'])
		{
			return $this->abandon($response, 'passwords do not match');
		}
		
		try
		{
			$user = UserExisting::constructByPasswordResetSecret($post['code']);
		}
		catch(Exception $e)
		{
			return $this->abandon($response, 'Code does not check out!');
		}
		$user->updatePassword($config, $post['password']);
		
		$page = (new PageRedirector())->withRedirectTo('/login');
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('Password has been reset. Please login.');
		
		return $response;
	}
}