<?php

class PageRedirector implements Page
{
	private string $redirectTo;
	
	private array $userNotices;
	private array $errors;
	
	public function __construct()
	{
		$this->redirectTo = '/home';
		$this->userNotices = [];
		$this->errors = [];
	}
	
	public function withExtraSiteNotices(array $siteNotices) : Page
	{
		return $this;
	}
	public function withExtraUserNotices(array $userNotices) : Page
	{
		return $this;
	}
	public function withExtraErrors(array $errors) : Page
	{
		return $this;
	}
	public function withUser(User $user) : Page
	{
		return $this;
	}
	public function withRedirectTo(string $redirectTo) : PageRedirector
	{
		$new = clone $this;
		$new->redirectTo = $redirectTo;
		return $new;
	}
	
	public function applyAdditionalHeaders() : void
	{
		header("Location: {$this->redirectTo}");
	}
	
	public function contentType() : string
	{
		return 'text/html';
	}
	public function toString() : string
	{
		return <<<HTML
<html>
<head><script>window.location.href='{$this->redirectTo}';</script></head>
<body>You are being redirected to <a href='{$this->redirectTo}'>{$this->redirectTo}</a></body>
</html>
HTML;
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