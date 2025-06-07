<?php

class EMail
{
	private string $from;
	private string $replyTo;
	private Array $to;
	private string $subject;
	private string $xmailer;
	private string $contentType;
	private string $contentTransferType;
	private string $body;
	
	static private string $EOL = "\n"; // because of Unix's sendmail bug
	
	public function __construct()
	{
		$this->from = 'webmaster@happypostcard.fun';
		$this->replyTo = $this->from;
		$this->to = [];
		$this->subject = '';
		$this->xmailer = 'PHP/'.phpversion();
		$this->contentType = 'text/plain; charset=utf-8';
		$this->contentTransferType = 'base64';
		$this->body = '';
	}
	
	public static function init(Config $config) : void
	{
	}
	
	public function withReplyTo(string $email, string $name='') : EMail
	{
		$new = clone $this;
		
		$unsafe= array('<', '>');
		$safe  = array('〈', '〉');
		$name = str_replace($unsafe, $safe, html_entity_decode($name));
		
		if(!empty($name))
		{
			$name = '=?UTF-8?B?' . base64_encode($name) . '?=';
			$email = "{$name} <{$email}>";
		}
		$new->replyTo = $email;
		
		return $new;
	}
	public function withExtraTo(string $email, string $name='') : Email
	{
		$new = clone $this;
		
		$unsafe= array('<', '>');
		$safe  = array('〈', '〉');
		$name = str_replace($unsafe, $safe, html_entity_decode($name));
		
		if(!empty($name))
		{
			$name = '=?UTF-8?B?' . base64_encode($name) . '?=';
			$email = "{$name} <{$email}>";
		}
		$new->to[] = $email;

		return $new;
	}
	public function withExtraBody(string $body) : EMail
	{
		$body = str_replace(["<br />", "<br>"], self::$EOL, str_replace(["\r", "\n"], '', nl2br($body)));
		
		$new = clone $this;
		$new->body .= $body;
		return $new;
	}
	public function withExtraNoscriptBody(string $body) : EMail
	{
		$body = str_replace(["<br />", "<br>"], self::$EOL, str_replace(["\r", "\n"], '', nl2br($body)));
		
		$unsafe= array('<', '>');
		$safe  = array('〈', '〉');
		
		$new = clone $this;
		$new->body .= str_replace($unsafe, $safe, html_entity_decode($body));
		return $new;
	}
	public function withSubject(string $subject) : EMail
	{
		$new = clone $this;
		$new->subject = $subject;
		return $new;
	}
	public function mail() : bool
	{
		$to = implode(', ', $this->to);
		$body = EMail::utf8_wordwrap($this->body, 75, self::$EOL);
		$body = chunk_split(base64_encode($body), 76, self::$EOL);
		$subject = '=?UTF-8?B?' . base64_encode($this->subject) . '?=';
		
		$headers = [
			'Content-Type' => $this->contentType,
			'Content-Transfer-Encoding' => $this->contentTransferType,
			'From' => $this->from,
			'Reply-To' => $this->replyTo,
			'X-Mailer' => $this->xmailer,
			'Date' => date("r (T)"),
			'Sensitivity' => 'Personal',
		];
		if($headers['From'] == $headers['Reply-To'])
		{
			unset($headers['Reply-To']);
		}
		
		$res = mail($to, $subject, $body, $headers);
		return $res;
	}
	public function mail_var_dump(bool $die = false) : void
	{
		$to = implode(', ', $this->to);
		$body = EMail::utf8_wordwrap($this->body, 75, self::$EOL);
		$body = chunk_split(base64_encode($body), 76, self::$EOL);
		$subject = '=?UTF-8?B?' . base64_encode($this->subject) . '?=';
		
		$headers = [
			'Content-Type' => $this->contentType,
			'Content-Transfer-Encoding' => $this->contentTransferType,
			'From' => $this->from,
			'Reply-To' => $this->replyTo,
			'X-Mailer' => $this->xmailer,
			'Date' => date("r (T)"),
			'Sensitivity' => 'Personal',
		];
		
		echo '<pre>';
		echo '$to=';
		var_dump(htmlspecialchars($to));
		echo '$subject=';
		var_dump(htmlspecialchars($subject));
		echo '$body=';
		var_dump(htmlspecialchars($body));
		echo '$headers=';
		var_dump($headers);
		echo '</pre>';
		if($die) die();
	}
	public function mailIndividually() : int
	{
		$count = 0;
		foreach($this->to as $to)
		{
			$body = EMail::utf8_wordwrap($this->body, 75);
			$body = chunk_split(base64_encode($body));
			$subject = '=?UTF-8?B?' . base64_encode($this->subject) . '?=';
			
			$headers = [
				'Content-Type' => $this->contentType,
				'Content-Transfer-Encoding' => $this->contentTransferType,
				'From' => $this->from,
				'Reply-To' => $this->replyTo,
				'X-Mailer' => $this->xmailer,
				'Date' => date("r (T)"),
				'Sensitivity' => 'Personal',
			];
			$res = mail($to, $subject, $body, $headers);
			if($res) ++$count;
		}
		return $count;
	}
	
	// https://www.php.net/manual/en/function.wordwrap.php
	static function utf8_wordwrap(string $string, int $width=75, string $break="\r\n", bool $cut=false)
	{
		$string .= self::$EOL; // force wrap of the last line correctly
		if($cut)
		{
			// Match anything 1 to $width chars long followed by whitespace,
			// otherwise match anything $width chars long
			$search= '/(.{1,'.$width.'})(?:\s)|(.{'.$width.'})(?!$)/uS';
			$replace = '$1$2'.$break;
		}
		else
		{
			// Anchor the beginning of the pattern with a lookbehind
			// to avoid crazy backtracking when words are longer than $width
			$search= '/(?<=\s|^)(.{1,'.$width.'}\S*)(?:\s)/uS';
			$replace = '$1'.$break;
		}
		return preg_replace($search, $replace, $string);
	}
}