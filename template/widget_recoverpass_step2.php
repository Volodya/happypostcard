<section class='important'>
	<header>Get recovery code</header>
	<form method='POST' action='/performrecoverpass'>
		<input type='hidden' name='step' value='2' />
		<div>
			Your reset code: <input type='text' name='code' required='required' />
		</div>
		<div>
			Password: <input type='password' name='password' required='required' />
		</div>
		<div>
			Repeat password: <input type='password' name='password2' required='required' />
		</div>
		<div>
			<button type='submit'>Reset my password</button>
		</div>
	</form>
</section>
