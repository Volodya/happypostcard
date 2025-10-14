<?php

class PerformerReceivePostcard extends Performer_Abstract
{
	public function sendEmail(Card $card, string $hurray = '') : void
	{
		$sender = $card->getSender();
		$receiver = $card->getReceiver();
		
		$cardCode = $card->getCode();
		
		$senderEmail = $sender->getEmail();
		$receiverEmail = $receiver->getEmail();
		$senderLocation = $sender->getHomeLocation();
		$receiverLocation = $receiver->getHomeLocation();
		
		$sentDate = $card->getSentDateTime()->format('Y-m-d');
		$daysTravelled = $card->getReceivedDateTime()->diff($card->getSentDateTime())->format('%a');
		
		$email = new EMail();
		
		$email = $email
			->withSubject("{$cardCode} from {$senderLocation['name']} was received [Happy Postcard]")
			->withExtraTo($receiverEmail['email'], $receiverEmail['polite_name'])
			->withExtraNoscriptBody(
				"Good time of the day, {$receiverEmail['polite_name']}!"
				.EMail::EOL.EMail::EOL
				."Thanks a lot for registering {$cardCode}. "
				."This time the sender of a Happy Postcard was {$senderEmail['polite_name']}. "
				."They are representing {$senderLocation['name']} as their chosen location."
				.EMail::EOL.EMail::EOL
				."The card was sent on {$sentDate} and took {$daysTravelled} days to arrive."
				.EMail::EOL.EMail::EOL
				."A message that you chose to send them as a Hurray:"
				.EMail::EOL
				.$hurray
			);
		
		$email->mail();
		
		$email = new EMail();
		
		$email = $email
			->withSubject("Hurray! {$cardCode} to {$receiverLocation['name']} was received [Happy Postcard]")
			->withExtraTo($senderEmail['email'], $senderEmail['polite_name'])
			->withExtraNoscriptBody(
				"Good time of the day, {$senderEmail['polite_name']}!"
				.EMail::EOL.EMail::EOL
				."Congratulations, the card {$cardCode} has arrived and has been registered by {$receiverEmail['polite_name']}. "
				."The location of the receiver is {$receiverLocation['name']}."
				.EMail::EOL.EMail::EOL
				."The card was sent on {$sentDate} and took {$daysTravelled} days to arrive."
				.EMail::EOL.EMail::EOL
				."A message that they chose to send you as a Hurray:"
				.EMail::EOL
				.$hurray
				.EMail::EOL.EMail::EOL
				."-- "
				.EMail::EOL
				."https://www.happypostcard.fun/card/{$cardCode}"
			);
		
		$email->mail();

	}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['code1', 'code2', 'code3', 'code4']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'code1' => [
				'custom_filter' => 'FILTER_SANITIZE_ALPHANUMERIC',
			],
			'code2' => [
				'custom_filter' => 'FILTER_SANITIZE_ALPHANUMERIC',
			],
			'code3' => [
				'custom_filter' => 'FILTER_SANITIZE_NUMERIC',
			],
			'code4' => [
				'custom_filter' => 'FILTER_SANITIZE_NUMERIC',
			],
			'message' => [
				'custom_filter' => 'FILTER_SANITIZE_NOSCRIPT',
				'default' => '',
			]
		]);
		
		$user = $request->getLoggedInUser();
		
		$code1 = Location::guessLocationByInputCode($post['code1']);
		$code2 = Location::guessLocationByInputCode($post['code2']);
		$code = strtoupper("{$code1}-{$code2}-{$post['code3']}-{$post['code4']}");
		
		try
		{
			$card = Card::constructByCode($code);
		}
		catch(Exception $e)
		{
			return $this->abandon($response, "This postcard ({$code}) does not exist or is addressed to a different person");
		}
		if($card->isRegistered())
		{
			$response = $response->withPage((new PageRedirector())->withRedirectTo('/card/'.$code));
			return $this->abandon($response, "This postcard ({$code}) has already been registered");
		}
		if($card->getReceiverId() != $user->getId())
		{
			return $this->abandon($response, "This postcard ({$code}) does not exist or is addressed to a different person");
		}
		
		$card->register();
		$card->getSender()->confirmAsSender();
		$card->getReceiver()->confirmAsReceiver();
		
		$this->sendEmail($card, $post['message']);
		
		$response = $response->withPage(
			(new PageRedirector())->withRedirectTo("/card/{$code}")
		);
		
		return $response;
	}
}