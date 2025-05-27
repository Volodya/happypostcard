<?php

abstract class JsonGenerator implements Page
{
	public function withExtraErrors(array $errors) : Page
	{
		return $this;
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
		return 'application/json';
	}
	public function isDisplayed() : bool
	{
		return false;
	}
	public function showsPerformerResults() : bool
	{
		return false;
	}
}