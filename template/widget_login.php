<section class='important'>
	<header>Login</header>
	<form method='POST' action='/performlogin'>
		<div><label>Name: <input type='text' name='login' autocomplete='on' required='required' /></label></div>
		<div><label>Password: <input type='password' name='password' autocomplete='on' required='required' /></label></div>
		<button type='submit'>Login</button>
		<button type='reset'>Reset</button>
	</form>
	<form method='GET' action='/recoverpass'>
		<button type='submit'>Recover password</button>
	</form>
	<form method='GET' action='/register'>
		<button type='submit'>Register</button>
	</form>
</section>