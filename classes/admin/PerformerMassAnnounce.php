<?php

class PerformerMassAnnounce extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		// SUPERADMIN SANITY CHECK
		if(!$request->isLoggedIn() or !$request->getLoggedInUser()->isAdmin())
		{
			return $this->abandon($response, 'Must be admin!');
		}
		
		if(!$request->allSetPOST(['subject', 'body', 'sql']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'sql' => [
				'filter' => FILTER_UNSAFE_RAW, // WARNING, NEED TO MAKE SURE THE PERSON IS SUPERADMIN
			],
			'subject' => [
				'custom_filter'=>'FILTER_SANITIZE_NOSCRIPT',
			],
			'body' => [
				'custom_filter'=>'FILTER_SANITIZE_NOSCRIPT',
			]
		]);
		
		$db = Database::getInstance();
		
		$stmt = $db->prepare($post['sql']);
		$res = $stmt->execute();
		if($res == false)
		{
			return $this->abandon($response, 'SQL error');
		}
		$senderEmail = [
			'email' => 'webmaster@happypostcard.fun',
			'polite_name' => 'Happy Postcard'
		];
		$email = (new EMail())
			->withReplyTo($senderEmail['email'], $senderEmail['polite_name'])
				->withSubject("{$post['subject']} [HappyPostcard]")
				->withExtraBody($post['body']) // Note: allowing poetential < and > characters, ADMIN!
		;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$address = [
				'email' => $row['email']
			];
			
			$email = $email->withExtraTo($address['email']/*, $address['polite_name']*/);
		}
		$res = $email->mailIndividually();
		if($res == 0)
		{
			return $this->abandon($response, '0 messages sent');
		}
		
		$response = $response->withNoticeMessage("Total announcements sent: {$res}");
		return $response;
	}
}