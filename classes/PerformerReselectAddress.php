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
				.EMail::EOL.EMail::EOL
				."Congratulations, you have an ability to send a postcard. "
				."This time {$receiverName} will be a Happy Recepient. "
				."They are representing {$receiverLocation['name']} as their chosen location."
				.EMail::EOL.EMail::EOL
				."Please write the code {$cardCode} on the postcard, and send it to:"
				.EMail::EOL.EMail::EOL
			);
		$firstAddress = true;
		foreach($receiverAddresses as $address)
		{
			if(!$firstAddress)
			{
				$email = $email->withExtraBody(" -- or --\r\n");
			}
			$email = $email->withExtraBody($address.EMail::EOL);
			$firstAddress = false;
		}
		$email = $email
			->withExtraBody(
				EMail::EOL
				."Some information about theirselves they chose to share:"
				.EMail::EOL
				.$receiverTextInfo['about']
				.EMail::EOL.EMail::EOL
				."They describe cards they wish to receive as:"
				.EMail::EOL
				.$receiverTextInfo['desires']
				.EMail::EOL.EMail::EOL
				."You can consider their hobbies that they have shared:"
				.EMail::EOL
				.$receiverTextInfo['hobbies']
				.EMail::EOL.EMail::EOL
				."Languages that they can understand are:"
				.EMail::EOL
				.$receiverTextInfo['languages']
				.EMail::EOL.EMail::EOL
				."Please try to stay away from themes that can cause this person emotional anguish:"
				.EMail::EOL
				.$receiverTextInfo['phobias']
				.EMail::EOL.EMail::EOL
				."-- "
				.EMail::EOL
				."https://www.happypostcard.fun/card/{$cardCode}"
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