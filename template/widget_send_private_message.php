<section>
	<header>Send a private message</header>
	<form method='POST' action='/performsendprivatemessage' />
		<input type='hidden' name='user' value='<?= $this->options['user']->getLogin() ?>' />
		<div><label>Subject: <input type='text' name='subject' required='required' /></label></div>
		<textarea name='body' cols='72' rows='15' required='required'></textarea>
		<div><button type='submit'>Send</button></div>
	</form>
</section>