<?php

class PerformerGetHelp extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['subject', 'body']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'subject' => [
				'custom_filter'=>'FILTER_SANITIZE_NOSCRIPT',
			],
			'body' => [
				'custom_filter'=>'FILTER_SANITIZE_NOSCRIPT',
			]
		]);
		$email = (new EMail())
			->withExtraTo('EchoOfFreedom@riseup.net')
			->withSubject("[HappyPostcard] Help: {$post['subject']}")
			->withExtraBody($post['body']);
		
		if($request->isLoggedIn())
		{
			$replyTo = $request->getLoggedInUser()->getEmail();
			$email = $email->withReplyTo($replyTo['email'], $replyTo['polite_name']);
		}
		//$email->mail_var_dump();
		$res = $email->mail();
		if(!$res)
		{
			return $this->abandon($response, 'Error sending a message');
		}
		$response = $response->withNoticeMessage('Thanks for sending a message to administrator');
		return $response;
	}
}