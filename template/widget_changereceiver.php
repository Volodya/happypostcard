<section>
	<header>Note!</header>
	<p>If you still want to change the receiver, you can do so below.</p>
	<form method='POST' action='/performreselectaddress'>
		<input type='hidden' name='code' value='<?= $this->options['card_code'] ?>' />
		<div><label>Your location:
			<select name='location'>
				<?php $this->complexWidgets->locationselectoptionlist_user($this->user); ?>
			</select>
		</label></div>
		<div>
			<label>
				Confirm that you are willing to send a postcard to a random person.
				<input type='checkbox' name='confirm' />
			</label>
		</div>
		<div><button>Get an address</button></div>
	</form>
</section>