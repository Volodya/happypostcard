<?php

class PerformerReselectAddress extends Performer_Abstract
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
			->withExtraBody(
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
		if(!$request->allSetPOST(['code']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'code' => [
				'filter' => FILTER_UNSAFE_RAW, // will check in database
			],
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
			return $this->abandon($response, 'You must confirm if you wish to really change the receiver.');
		}
		
		$user = $request->getLoggedInUser();
		
		$senderId = $user->getId();
		$code = $post['code'];
		try
		{
			$senderLocationId = Location::getIdByCode($post['location']);
		}
		catch(Exception $ex)
		{
			return $this->abandon($response, 'Something went horribly wrong, location you have selected does not exist!');
		}
		
		$card = Card::constructByCode($code);
		if($card->getSenderId() != $senderId)
		{
			return $this->abandon($response, 'You are not the sender of this card');
		}
		try
		{
			$card = $card->changeReceiver($senderLocationId);
		}
		catch(Exception $ex)
		{
			return $this->abandon($response, 'No user is currently available to send postcards to, your card&apos;s destination is left unchanged');
		}
		
		$this->sendEmail($card);
		
		$response = $response->withPage(
			(new PageRedirector())->withRedirectTo('/card/'.$card->getCode())
		);
		
		return $response;
	}
}