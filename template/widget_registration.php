<section class='important'>
	<header>Registration</header>
	<form method='POST' action='/performregistration'>
		<div><label>Login: <input type='text' name='login' required='required' autocomplete='on' autocorrect='off' spellcheck='false' required='required'
			placeholder='Nickname' /></label></div>
		<div><label>Email: <input type='email' name='email' required='required' autocomplete='on' autocorrect='off' spellcheck='false' required='required'
			placeholder='Address' /></label></div>
		<div><label>Password: <input type='password' name='password' required='required'
			placeholder='secret' /></label></div>
		<div><label>Repeat password: <input type='password' name='password2' required='required'
			placeholder='secret' /></label></div>
		<div><label>Your home location: <select name='home_location' id='home_location'><?php $this->complexWidgets->locationselectoptionlist(); ?></select></label></div>
		<?php $this->complexWidgets->locationselection_about('home_location'); ?>
		<button type='submit'>Register</button>
		<button type='reset'>Reset</button>
	</form>
	<form method='GET' action='/login'>
		<button type='submit'>Already have an account</button>
	</form>
</section>