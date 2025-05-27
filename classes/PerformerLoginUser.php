<?php

class PerformerLoginUser extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['login', 'password']))
		{
			$this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST( [
			'login' => [
				'custom_filter' => 'FILTER_SANITIZE_LOGIN',
			],
			'password' => [
				'filter' => FILTER_UNSAFE_RAW
			],
		] );
		
		if(!User::exists($post['login']))
		{
			return $this->abandon($response, 'User does not exist, maybe you typed login incorrectly '.base64_encode($post['login']));
		}
		
		try
		{
			$user = UserExisting::constructByLogin($post['login']);
		}
		catch(Exception $ex) // is probably unnecessary, just being pedantic
		{
			return $this->abandon($response, 'There was an error during the login process');
		}
		if(!$user->canLogin($post['password'], $config))
		{
			return $this->abandon($response, 'Password is incorrect');
		}
		$user->updateLoggedInDate();
		
		$secret = bin2hex(random_bytes(256/2));
		$user->updateSecret($secret);
		$response->setCookie('persistent', $secret);
		
		$response->setSession('login_id', $user->getId());
		$response = $response->withNoticeMessage("You have been logged in as {$post['login']}.");
		
		if(!$user->hasAddress())
		{
			$response = $response->withNoticeMessage(
				"You have no home address set. Please set address in <a href='/useredit/{$post['login']}'>Profile</a>!"
			);
		}
		if($user->isTravelling())
		{
			$response = $response->withNoticeMessage("You have a travelling location set, you can send, but will not be
				receiving postcards. Once you stop travelling, you can change that in 
				<a href='/useredit/{$post['login']}'>Profile</a>"
			);
		}
		if(!$user->isEnabled())
		{
			$response = $response->withNoticeMessage("You have previously temporarily disabled your account.
				When you are once again able to send postcards, please indicate that in your
				<a href='/useredit/{$post['login']}'>Profile</a>"
			);
		}
		return $response;
	}
	
}