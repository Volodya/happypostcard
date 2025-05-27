<?php

class Page500 implements Page
{
	private string $additionalError;
	
	public function __construct(string $additionalError = '')
	{
		$this->additionalError = $additionalError;
	}
	public function withExtraSiteNotices(array $userNotices) : Page
	{
		return $this;
	}
	public function withExtraUserNotices(array $userNotices) : Page
	{
		return $this;
	}
	public function withUser(User $user) : Page
	{
		return $this;
	}
	public function applyAdditionalHeaders() : void
	{}
	public function contentType() : string
	{
		return 'text/html';
	}
	public function toString() : string
	{
		return "<html><body><h1><center>500 internal server error</center></h1><hr/><center>{$this->additionalError}</center></body></html>";
	}
	public function isDisplayed() : bool
	{
		return true;
	}
	public function showsPerformerResults() : bool
	{
		return false;
	}
}