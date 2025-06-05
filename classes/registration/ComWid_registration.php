<?php

class ComWid_registration implements ComWid
{
	private bool $displayed;
	private array $guessedLocation;
	
	public function __construct()
	{
		$this->displayed = false;
		$this->guessedLocation = [];
	}
	public function setEditor(User $editor) : void
	{}
	public function setTemplateParameter(array $parameter) : void
	{}
	public function setPerformerResults($performerResults) : void
	{
		if(isset($performerResults['location'])) $this->guessedLocation=$performerResults['location'];
	}
	public function invoke() : void
	{
		$guessedLocationCode = 'SOL3';
		?><section class='important'>
			<header>Registration</header>
				<form method='POST' action='/performregistration'>
				<div><label>Login: <input type='text' name='login' required='required' autocomplete='on' autocorrect='off' spellcheck='false' required='required'
					placeholder='Login' /></label></div>
				<div><label>Email: <input type='email' name='email' required='required' autocomplete='on' autocorrect='off' spellcheck='false' required='required'
					placeholder='Email address' /></label></div>
				<div><label>Password: <input type='password' name='password' required='required'
					placeholder='secret' /></label></div>
				<div><label>Repeat password: <input type='password' name='password2' required='required'
					placeholder='secret' /></label></div><?php
				
				if(!empty($this->guessedLocation) and $this->guessedLocation['code'] != 'SOL3')
				{
					$guessedLocationCode=$this->guessedLocation['code'];
					?><p>The system has determined that your location is <?= $this->guessedLocation['name'] ?>, if that
					is not correct, please change it below.</p><?php
				}
								
				?><div><label>Your home location: <select name='home_location' id='home_location'><?php
					HtmlSnippets::printLocationSelectOptionList($guessedLocationCode);
				?></select></label></div>
				<?php HtmlSnippets::printLocationSelection_About('home_location'); ?>
				<button type='submit'>Register</button>
				<button type='reset'>Reset</button>
			</form>
			<form method='GET' action='/login'>
				<button type='submit'>Already have an account</button>
			</form>
		</section><?php
		
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}