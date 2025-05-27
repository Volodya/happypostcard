<?php

class Response
{
	private int $errorCode;
	
	private array $errorMessages;
	private array $noticeMessages;
	
	private Page $page;
	
	public function __construct()
	{
		$this->errorCode = 500;
		$this->page = new Page500('Page has not been initialised'); // needs to be substituted for something
		$this->errorMessages = [];
		$this->noticeMessages = [];
	}
	
	public function withErrorMessage(string $errorMessage) : Response
	{
		$new = clone $this;
		$new->errorMessages[] = $errorMessage;
		return $new;
	}
	
	public function withNoticeMessage(string $noticeMessage) : Response
	{
		$new = clone $this;
		$new->noticeMessages[] = $noticeMessage;
		return $new;
	}
	
	public function withPage(Page $page, $errorCode = 200) : Response
	{
		$new = clone $this;
		$new->page = $page;
		$new->errorCode=$errorCode;
		return $new;
	}
	public function getPage() : Page
	{
		return $this->page;
	}
	
	public function send() : void
	{
		header('Content-Type: '.$this->page->contentType());
		http_response_code($this->errorCode);
		
		if($this->page->isDisplayed()) // add stored messages
		{
			$this->page = $this->page
				->withExtraErrors($this->errorMessages)
				->withExtraUserNotices($this->noticeMessages);
		}
		
		echo $this->page->toString();
		
		if($this->page->isDisplayed()) // wipe messages in SESSION (they are displayed)
		{
			$this->wipeUserNotices();
		}
		else // otherwise add current errors to be displayed next time
		{
			foreach($this->noticeMessages as $notice)
			{
				$this->addNotice($notice);
			}
			foreach($this->errorMessages as $error)
			{
				$this->addError($error);
			}
		}
	}
	
	private function addError(string $errorMessage)
	{
		if(isset($_SESSION['errors']) && is_array($_SESSION['errors']))
		{
			$_SESSION['errors'] = array_merge( $_SESSION['errors'], [ $errorMessage ] );
		}
		else
		{
			$_SESSION['errors'] = [ $errorMessage ];
		}
	}
	private function addNotice(string $notice)
	{
		if(isset($_SESSION['notices']) && is_array($_SESSION['notices']))
		{
			$_SESSION['notices'] = array_merge( $_SESSION['notices'], [ $notice ] );
		}
		else
		{
			$_SESSION['notices'] = [ $notice ];
		}
	}
	public function wipeUserNotices()
	{
		$this->unsetSession('notices');
		$this->unsetSession('errors');
	}
	public function setSession(string $key, string $value) : void
	{
		$_SESSION[$key] = $value;
	}
	public function unsetSession(string $key) : void
	{
		unset($_SESSION[$key]);
	}
	public function setCookie(string $key, string $value) : void
	{
		$res = setcookie($key, $value, [
			'expires' => time()+60*60*24*30, // 30 days
			'httponly' => true,
			'samesite' => 'Strict',
		]);
		if($res === false)
		{
			echo 'Set cookie failed';
			die();
		}
	}
}