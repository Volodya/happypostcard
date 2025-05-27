<?php

class PageJsonResponse implements Page
{
	protected JsonGenerator $json;
	public function __construct(JsonGenerator $json)
	{
		$this->json = $json;
	}
	public function withExtraSiteNotices(array $userNotices) : Page
	{
		return $this;
	}
	public function withExtraUserNotices(array $userNotices) : Page
	{
		return $this;
	}
	public function withExtraErrors(array $userNotices) : Page
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
		return 'application/json';
	}
	public function toString() : string
	{
		return $this->json.toString();
	}
	public function isDisplayed() : bool
	{
		return true;
	}
}