<?php

abstract class Page_Abstract implements Page
{
	protected Template $templated;
	
	public function withExtraSiteNotices(array $siteNotices) : Page
	{
		$new = clone $this;
		$new->templated = $this->templated->withExtraSiteNotices($siteNotices);
		return $new;
	}
	public function withExtraUserNotices(array $userNotices) : Page
	{
		$new = clone $this;
		$new->templated = $this->templated->withExtraUserNotices($userNotices);
		return $new;
	}
	public function withExtraErrors(array $errors) : Page
	{
		$new = clone $this;
		$new->templated = $this->templated->withExtraErrors($errors);
		return $new;
	}
	public function withUser(User $user) : Page
	{
		$new = clone $this;
		
		$new->templated = $this->templated->withUser($user);
		
		return $new;
	}
	public function applyAdditionalHeaders() : void
	{}
	
	public function contentType() : string
	{
		return 'text/html';
	}
	public function toString() : string
	{
		return $this->templated->toString();
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