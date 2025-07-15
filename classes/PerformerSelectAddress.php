<?php

class PerformerSelectAddress extends Performer_Abstract
{
	public function sendEmail(Card $card) : void
	{
		$cardCode = $card->getCode();
		$sender = $card->getSender();
		$receiver = $card->getReceiver();
		
		$senderEmail = $sender->getEmail();
		$receiverName = $receiver->getPoliteName();
		$receiverLocation = $receiver->getHomeLocation();
		$receiverTextInfo = $receiver->getTextInformation();
		
		$receiverAddresses = $receiver->getAddresses();
		
		$email = new EMail();
		
		$email = $email
			->withSubject("{$cardCode} is going to {$receiverLocation['name']} [Happy Postcard]")
			->withExtraTo($senderEmail['email'], $senderEmail['polite_name'])
			->withExtraNoscriptBody(
				"Good time of the day, {$senderEmail['polite_name']}!"
				."\r\n"."\r\n"
				."Congratulations, you have an ability to send a postcard. "
				."This time {$receiverName} will be a Happy Recepient. "
				."They are representing {$receiverLocation['name']} as their chosen location."
				."\r\n"."\r\n"
				."Please write the code {$cardCode} on the postcard, and send it to:"
				."\r\n"."\r\n"
			);
		$firstAddress = true;
		foreach($receiverAddresses as $address)
		{
			if(!$firstAddress)
			{
				$email = $email->withExtraBody(" -- or --\r\n");
			}
			$email = $email->withExtraBody($address."\r\n");
			$firstAddress = false;
		}
		$email = $email
			->withExtraNoscriptBody(
				"\r\n"
				."Some information about theirselves they chose to share:"
				."\r\n"
				.$receiverTextInfo['about']
				."\r\n"."\r\n"
				."They describe cards they wish to receive as:"
				."\r\n"
				.$receiverTextInfo['desires']
				."\r\n"."\r\n"
				."You can consider their hobbies that they have shared:"
				."\r\n"
				.$receiverTextInfo['hobbies']
				."\r\n"."\r\n"
				."Languages that they can understand are:"
				."\r\n"
				.$receiverTextInfo['languages']
				."\r\n"."\r\n"
				."Please try to stay away from themes that can cause this person emotional anguish:"
				."\r\n"
				.$receiverTextInfo['phobias']
				."\r\n"."\r\n"
			);
		
		$email->mail();
	}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['location']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'location' => [
				'filter' => FILTER_UNSAFE_RAW, // will check in database
			],
			'confirm' => [
				'filter' => FILTER_UNSAFE_RAW, // will disallow anything, but 'on' value
				'default' => 'off',
			]
		]);
		
		if($post['confirm'] != 'on')
		{
			return $this->abandon($response, 'You must confirm that you are willing to send out the card.');
		}
		
		$sender = $request->getLoggedInUser();
		if(($reason = $sender->reasonCannotSend()) != '')
		{
			switch($reason)
			{
				case 'BLOCKED':
					$response = $response->withPage(
						(new PageRedirector())->withRedirectTo("/help")
					);
					return $this->abandon($response, "You have been blocked by an administrator, you will be unable to send more postcards.
				Please <a href='/help'>contact</a> an administrator if you need further information!");
				case 'NO ADDRESS':
					$response = $response->withPage(
						(new PageRedirector())->withRedirectTo("/useredit/{$sender->getLogin()}")
					);
					return $this->abandon($response, 'You must fill out your address');
				case '24 TRAVELLING LT6 SENT':
					$response = $response->withPage(
						(new PageRedirector())->withRedirectTo("/travelling")
					);
					return $this->abandon($response, 'You have requested 24 addresses. Please wait for some of them to arrive.');
				case '24 TRAVELLING 6 SENT 0 WAITING+RECEIVED':
					$response = $response->withPage(
						(new PageRedirector())->withRedirectTo("/useredit/{$sender->getLogin()}")
					);
					return $this->abandon($response, 'You have sent 24 addresses, while people cannot send anything to you. Please make sure you have enabled receiving postcards.');
				case '24 SENT 0 WAITING+RECEIVED':
					$response = $response->withPage(
						(new PageRedirector())->withRedirectTo("/useredit/{$sender->getLogin()}")
					);
					return $this->abandon($response, 'You have sent 24 postcards, while people cannot send anything to you. Please make sure you have enabled receiving postcards.');
			}
		}
		
		$senderLocationCode = $post['location'];
		try
		{
			$senderLocationId = Location::getIdByCode($post['location']);
		}
		catch(Exception $ex)
		{
			return $this->abandon($response, 'Something went horribly wrong, location you have selected does not exist!');
		}
		
		try
		{
			if(!$request->allSetPOST(['type', 'receiver_login']))
			{
				$card = Card::sendCard($sender, $senderLocationId);
			}
			else
			{
				$post = array_merge($post, $request->getPOST([
					'receiver_login' => [
						'custom_filter' => 'FILTER_SANITIZE_LOGIN'
					],
					'type' => [
						'filter' => FILTER_SANITIZE_NUMBER_INT
					],
				]));
				$receiver = UserExisting::constructByLogin($post['receiver_login']);
				if($post['type']!=2)
				{
					return $this->abandon($response, 'Something went horribly wrong, wrong type!');
				}
				$card = Card::sendCardToUser($sender, $senderLocationId, $receiver, $post['type']);
			}
		}
		catch(Exception $ex)
		{
			$response = $response->withPage(
				(new PageRedirector())->withRedirectTo("/travelling")
			);
			return $this->abandon($response, 'No user is currently available to send postcards to');
		}
		
		$this->sendEmail($card);
		
		$response = $response->withPage(
			(new PageRedirector())->withRedirectTo('/card/'.$card->getCode())
		);
		
		
		if(!$sender->hasAddress())
		{
			$login = $sender->getLogin();
			$response = $response->withNoticeMessage(
				"You have no home address set. Please set address in <a href='/user/{$login}'>Profile</a>!"
			);
		}
		if($sender->isTravelling())
		{
			$login = $sender->getLogin();
			$response = $response->withNoticeMessage("You have a travelling location set, you can send, but will not be
				receiving postcards. Once you stop travelling, you can change that in 
				<a href='/useredit/{$login}'>Profile</a>"
			);
		}
		if(!$sender->isEnabled())
		{
			$login = $sender->getLogin();
			$response = $response->withNoticeMessage("You have previously temporarily disabled your account.
				When you are once again able to send postcards, please indicate that in your
				<a href='/useredit/{$login}'>Profile</a>"
			);
		}
		
		return $response;
	}
}