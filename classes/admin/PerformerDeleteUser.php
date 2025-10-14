<?php

class PerformerDeleteUser extends Performer_Abstract
{
	public function sendEmailToDeletedUser(UserExisting $deletedUser, string $secret)
	{
		$deletedUserEmail = $deletedUser->getEmail();
		
		$email = new EMail();
		
		$email = $email
			->withSubject("{$cardCode} to {$receiverLocation['name']} was auto-registered [Happy Postcard]")
			->withExtraTo($deletedUserEmail['email'], $deletedUserEmail['polite_name'])
			->withExtraNoscriptBody(
				"Good time of the day, {$deletedUserEmail['polite_name']}!"
				.EMail::EOL.EMail::EOL
				."Your profile has now been deleted. If you will want to recover it and take another attempt at"
				." participating in this hobby, please contact administrator and give them the following secret code:"
				.EMail::EOL
				.$secret
				.EMail::EOL.EMail::EOL
				."No further communication will be possible via the site, and you will not receive automatic updates. "
				."All of the profile information is deleted."
				."-- "
				.EMail::EOL
				."https://www.happypostcard.fun/"
			);
		
		$email->mail();
	}
	public function sendEmailToSender(Card $card) : void
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
			->withSubject("{$cardCode} to {$receiverLocation['name']} was auto-registered [Happy Postcard]")
			->withExtraTo($senderEmail['email'], $senderEmail['polite_name'])
			->withExtraNoscriptBody(
				"Good time of the day, {$senderEmail['polite_name']}!"
				.EMail::EOL.EMail::EOL
				."Thank you for sending {$cardCode} to {$receiverEmail['polite_name']}, "
				."who was registered as being in {$receiverLocation['name']}."
				.EMail::EOL.EMail::EOL
				."The card was sent on {$sentDate} and but after {$daysTravelled} days not registered by the receiver. "
				."Who has now had their account deleted."
				.EMail::EOL.EMail::EOL
				."The card has now been registered by the system."
				.EMail::EOL.EMail::EOL
				."-- "
				.EMail::EOL
				."https://www.happypostcard.fun/card/{$cardCode}"
			);
		
		$email->mail();
	}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['login']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'login' => [
				'filter' => FILTER_UNSAFE_RAW,
			],
		]);
		
		$editor = $request->getLoggedInUser();
		try
		{
			$deletedUser = UserExisting::constructByLogin($post['login']);
		}
		catch(Exception $ex)
		{
			return $this->abandon($response, 'User does not exist.');
		}
		
		if($editor->getId() != $deletedUser->getId() and !$editor->isAdmin())
		{
			return $this->abandon($response, 'Cannot delete another person\'s account.');
		}
		
		foreach($deletedUser->getWaitingPostcards() as $code)
		{
			$card = Card::constructByCode($code);
			$card->register(true);
			$this->sendEmailToSender($card);
		}
		
		$secret = $deletedUser->deleteUser();
		
		$this->sendEmailToDeletedUser($deletedUser, $secret);
		
		$response = $response->withPage(
			(new PageRedirector())->withRedirectTo("/admin")
		);
		
		return $response;
	}
}