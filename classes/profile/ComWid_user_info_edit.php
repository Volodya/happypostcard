<?php

class ComWid_user_info_edit implements ComWid
{
	private User $user;
	private User $editor;
	
	private bool $displayed;
	
	public function __construct()
	{
		$this->displayed = false;
	}
	public function setEditor(User $editor) : void
	{
		$this->editor = $editor;
	}
	public function setTemplateParameter(array $parameter) : void
	{
		$this->user = $parameter['user'];
	}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		if($this->user->getId() == $this->editor->getId())
		{
			// all is well
		}
		elseif($this->editor->isAdmin())
		{
			?>
				You are an admin and are editing another user's profile.
			<?php
		}
		else
		{
			?>
				You are not allowed to edit another user&apos;s profile.
			<?php
			return;
		}
		$userInfo = $this->user->getUserInfo();
		$addresses = $this->user->getUserAddresses();
		?>
		<form method='POST' action='/performprofileedit' enctype='multipart/form-data'>
			<div>Login: <tt><?= $this->user->getLogin() ?></tt><input type='hidden' name='login' value='<?= $this->user->getLogin() ?>' /></div>
			<div>Name: <input name='polite_name' value='<?= $userInfo['polite_name'] ?>' /></div>
			<div>Days on this site: <?= $userInfo['days_registered'] ?></div>
			<div>Birthday: <input type='date' name='birthday' value='<?= $userInfo['birthday'] ?>' /></div>
			<div>Home location: <select name='home_location' id='home_location'>
				<?php HtmlSnippets::printLocationSelectOptionList($userInfo['home_location_code']) ?>
			</select></div>
			<?php HtmlSnippets::printLocationSelection_About('home_location', 'home_location_about'); ?>
			<?php HtmlSnippets::printLocationSelection_CodeEntry('home_location', 'home_location_codeentry'); ?>
			
			<div>Address (with name):
				<div class='addresses_input'>
					<?php foreach($addresses as $addr) { ?>
						<div class='address_input'>
							<input type='hidden' name='addr_id[]' value='<?php echo $addr['id'] ?>' />
							<textarea name='addr_addr[]' rows='6' cols='27'><?php echo $addr['addr'] ?></textarea>
							<input type='text' name='addr_lang_code[]' value='<?php echo $addr['language_code'] ?>' />
						</div>
					<?php } ?>
					<div class='address_input'>
						<input type='hidden' name='addr_id[]' value='0' />
						<textarea name='addr_addr[]' rows='6' cols='27' placeholder='Your address in a different script/language'></textarea>
						<input type='text' name='addr_lang_code[]' value='en' />
					</div>
				</div>
			</div>
			<div>About yourself: <div class='userinfo_input'>
					<textarea name='about' rows='15' cols='27'
						placeholder='Introduce yourself'><?php echo $userInfo['about'] ?></textarea>
				</div>
			</div>
			<div>Your postcard preferences: <div class='userinfo_input'>
					<textarea name='desires' rows='15' cols='27'
						placeholder='What type of cards you enjoy most of all'><?php echo $userInfo['desires'] ?></textarea>
				</div>
			</div>
			<div>Hobbies:
				<input name='hobbies'
					placeholder='What are your hobbies?' value='<?php echo $userInfo['hobbies'] ?>' />
			</div>
			<div>Languages:
				<input name='languages'
					placeholder='Languages you undersand' value='<?php echo $userInfo['languages'] ?>' /></div>
			<div>Phobias:
				<input name='phobias'
					placeholder='Topics to avoid with you' value='<?php echo $userInfo['phobias'] ?>' /></div>
			<input type='submit' value='Save' />
		</form>
		<?php
		
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}