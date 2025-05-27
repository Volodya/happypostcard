<?php

interface Page
{
	public function withExtraSiteNotices(array $siteNotices) : Page;
	public function withExtraUserNotices(array $userNotices) : Page;
	public function withUser(User $user) : Page;
	public function applyAdditionalHeaders() : void;
	public function contentType() : string;
	public function toString() : string;
	public function showsPerformerResults() : bool;
	public function isDisplayed() : bool;
}