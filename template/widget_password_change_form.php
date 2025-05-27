<section>
	<header>Change Pasword</header>
	<form method='POST' action='/performpasswordchange'>
		<div><label>Current Password: <input type='password' name='currpassword' autocomplete='off' required='required' /></label></div>
		<div><label>New Password: <input type='password' name='newpassword' autocomplete='off' required='required' /></label></div>
		<div><label>New Password: <input type='password' name='newpassword2' autocomplete='off' required='required' /></label></div>
		<button type='submit'>Change</button>
		<button type='reset'>Reset</button>
	</form>
</section>