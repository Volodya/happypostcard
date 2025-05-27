<section>
	<header>Register the postcard</header>
	<form method='POST' action='/performreceivepostcard'>
		<div><label>
			Postcard code: <?
			?><input type='text' name='code1' class='uppercase'
				minlength='3' maxlength='5' autocomplete='on' autocorrect='off'  spellcheck='false' required='required'
				oninput="this.value=this.value.toUpperCase()"
				placeholder='Sender location' /><?
			?><input type='text' name='code2' class='uppercase'
				minlength='3' maxlength='5' autocomplete='on' autocorrect='off'  spellcheck='false' required='required'
				oninput="this.value=this.value.toUpperCase()"
				placeholder='Receiver location' /><?
			?><input type='number' name='code3' class='uppercase'
				min='2023' max='<?php echo date("Y"); ?>'
				placeholder='Year' /><?
			?><input type='number' name='code4' class='uppercase'
				min='1'
				placeholder='Sequence num' />
		</label></div>
		<div><label>Message to the sender:</label></div>
		<textarea name='message' rows='6' cols='45' maxlength='9000' wrap='soft' autocorrect='on' spellcheck='true'
			placeholder='Hurray' ></textarea>
		<div><button type='submit'>Register</button></div>
	</form>
</section>