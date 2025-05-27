<?php

class PerformerSendPrivateMessage extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['subject', 'body', 'user']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'user' => [
				'filter' => FILTER_UNSAFE_RAW, // will check DB and remove right away
			],
			'subject' => [
				'custom_filter'=>'FILTER_SANITIZE_NOSCRIPT',
			],
			'body' => [
				'custom_filter'=>'FILTER_SANITIZE_NOSCRIPT',
			]
		]);
		
		$recepient = User::constructByLogin($post['user']);
		$post['user'] = null; // UNSAFE_RAW
		
		if(get_class($recepient) != 'UserExisting')
		{
			return $this->abandon($response, 'You are sending a message to an unregistered user!');
		}
		
		if($request->isLoggedIn())
		{
			$sender = $request->getLoggedInUser();
			$senderLogin = $sender->getLogin();
			$senderEmail = $sender->getEmail();
			$senderName = $senderEmail['polite_name'];
			
			$email = (new EMail())
				->withExtraTo($senderEmail['email'], $senderEmail['polite_name'])
				->withSubject("[HappyPostcard] PM (copy): {$post['subject']}")
				->withExtraNoscriptBody($post['body'])
			;
			
			$email->mail();
			
		}
		else
		{
			return $this->abandon($response, 'You must login to send a private message');
		}
		
		$address = $recepient->getEmail();
		
		$email = (new EMail())
			->withExtraTo($address['email']/*, $address['polite_name']*/)
			->withSubject("[HappyPostcard] PM: {$post['subject']}")
			->withExtraNoscriptBody($post['body'])
		;
		
		$email = $email->withExtraBody(
			"\r\n\r\n-- \r\n"
			."This is a private message from {$senderName} on Happy Postcard\r\n"
//				."You can respond to them at http://www.happypostcard.fun/user/{$sender}"
			);
		
		
		$res = $email->mail();
		if(!$res)
		{
			return $this->abandon($response, 'Error sending a message');
		}
		
		$response = $response->withNoticeMessage('Your private message has been sent');
		return $response;
	}
}