<section>
	<header>Ready to make somebody happy with a Happy Postcard</header>
	<form method='POST' action='/performselectaddress'>
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
		<div><?php HtmlSnippets::printOneTimeButton(['type'=> 'submit'], 'Get an address') ?></div>
	</form>
</section>