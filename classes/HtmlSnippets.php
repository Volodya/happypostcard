<?php

class HtmlSnippets
{
	public static function __callStatic(string $method, array $parameters)
    {
		if(str_starts_with($method, 'print'))
		{
			return HtmlSnippets::get($method, $parameters);
		}
		if(str_starts_with($method, 'get'))
		{
			$newMethod = 'print'.substr($method, strlen('get'));
			return HtmlSnippets::get($newMethod, $parameters);
		}
		throw new Exception("No method '{$method}' in HtmlSnippets!");
	}
	
	public static function get(string $method, array $params) : string
	{
		ob_start();
		call_user_func_array("self::{$method}", $params);
		return ob_get_clean();
	}
	
	public static function printRandomUUID() : void
	{
		//https://stackoverflow.com/a/38897996/2893496
		printf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0C2f ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B )
		);
	}
	public static function printPhotoThumb200(Image $photo, string $user, bool $link=false, bool $canEdit=false) : void
	{
		$hash = $photo->getHash();
		$imageUrl = $photo->getThumb200();
		$imageTag = "<img src='{$imageUrl}' title='{$user}' />";
		if($link)
		{
			$imageTag = "<a href='/user/{$user}'><div class='thumbimage'>{$imageTag}</div><div class='thumbtext'>{$user}</div></a>";
		}
		else
		{
			$imageTag = "<a href='/image/{$hash}'><div class='thumbimage'>{$imageTag}</div></a>";
		}
		if($canEdit)
		{
			$imageTag .= "
				<form action='/performunlinkimage' method='post'>
					<input type='hidden' name='user' value='{$user}' />
					<input type='hidden' name='hash' value='{$hash}' />
					<button type='submit'>delete</button>
				</form>";
		}
		$imageTag = "<div class='thumb photothumb thumb200'>{$imageTag}</div>";
		echo $imageTag;
	}
	public static function printPostcardThumb200(
		string $hash, string $ext, string $code, bool $link=false, bool $canEdit=false, bool $received=true
	) : void
	{
		$imageUrl = '/' . Picture::dirThumbs . "/200thumbs/{$hash}.{$ext}";
		$imageTag = "<img src='{$imageUrl}' title='{$code}' />";
		if($link)
		{
			$imageTag = "<a href='/card/{$code}'><div class='thumbimage'>{$imageTag}</div><div class='thumbtext'>{$code}</div></a>";
		}
		else
		{
			$imageTag = "<a href='/image/{$hash}'><div class='thumbimage'>{$imageTag}</div></a>";
		}
		if($canEdit)
		{
			$imageTag .= "
				<form action='/performunlinkimage' method='post'>
					<input type='hidden' name='code' value='{$code}' />
					<input type='hidden' name='hash' value='{$hash}' />
					<button type='submit'>delete</button>
				</form>";
		}
		$classes = 'thumb cardthumb thumb200';
		if(!$received)
		{
			$classes .= ' cardthumbtravelling';
		}
		$imageTag = "<div class='{$classes}'>{$imageTag}</div>";
		echo $imageTag;
	}
	public static function printTimeClock(string $time) : void
	{
		static $CLOCKS = [
			 0 => [ 0 => '&#128347;', 30 => '&#128359;' ],
			 1 => [ 0 => '&#128336;', 30 => '&#128348;' ],
			 2 => [ 0 => '&#128337;', 30 => '&#128349;' ],
			 3 => [ 0 => '&#128338;', 30 => '&#128350;' ],
			 4 => [ 0 => '&#128339;', 30 => '&#128351;' ],
			 5 => [ 0 => '&#128340;', 30 => '&#128352;' ],
			 6 => [ 0 => '&#128341;', 30 => '&#128353;' ],
			 7 => [ 0 => '&#128342;', 30 => '&#128354;' ],
			 8 => [ 0 => '&#128343;', 30 => '&#128355;' ],
			 9 => [ 0 => '&#128344;', 30 => '&#128356;' ],
			10 => [ 0 => '&#128345;', 30 => '&#128357;' ],
			11 => [ 0 => '&#128346;', 30 => '&#128358;' ],
		];
		
		$timeParts = explode(':', $time);
		if(count($timeParts)<2)
		{
			return;
		}
		try
		{
			$hours = intval($timeParts[0]);
			
			$minutes = intval($timeParts[1]);
			if($minutes>45)
			{
				$minutes = 0;
				$hours = ($hours+1) % 24;
			}
			else if($minutes<15)
			{
				$minutes = 0;
			}
			else
			{
				$minutes = 30;
			}
			
			$ampm = ($hours < 12) ? 'am' : 'pm';
			$hours %= 12;
		}
		catch(Exception $e)
		{
			return;
		}
		
		?><span class='clock <?= $ampm ?>' title='<?= $time ?>'><?= $CLOCKS[$hours][$minutes] ?></span><?php
	}
	public static function printCircledDigits(int $num) : void
	{
		static $circled = mb_str_split('⓪①②③④⑤⑥⑦⑧⑨⑩⑪⑫⑬⑭⑮⑯⑰⑱⑲⑳㉑㉒㉓㉔㉕㉖㉗㉘㉙㉚㉛㉜㉝㉞㉟');
		if($num < count($circled))
		{
			echo $circled[$num];
			return;
		}
		$output = '';
		while($num != 0)
		{
			$digit = $num % 10;
			
			$output = $circled[$digit] . $output;
			
			$num = intdiv($num, 10);
		}
		
		echo $output;
	}
	public static function printImageUploadForm(string $type, string $what) : void // type is card|photo, what is either a card code or a user login
	{
		$id = uniqid();
		?>
		<div class='uploadform'>
		<form method='POST' action='/performupload' enctype='multipart/form-data' id='<?= $id ?>'>
			<input type='hidden' name='type' value='<?= $type ?>' />
			<input type='hidden' name='what' value='<?= $what ?>' />
			<input type='file' name='image' accept='image/*' />
			<button type='submit'>Upload</button>
		</form>
		<script>
			const form=document.getElementById('<?= $id ?>');
			const input=form.querySelector('input[type=file]');
			if(!!input)
			{
				input.addEventListener('change', hashfile(form, input, function(hash) {
					console.log('File hash is '+hash);
					
					getJson('/api_imagecount/'+hash, (err, data) => {
						if(data['total_count'] != 0)
						{
							linkHash(form, hash);
						}
						else
						{
							form.onsubmit=null;
						}
					});
				}));
			}
		</script>
		</div>
		<?php
	}
	public static function printOneTimeButton(array $options, string $innerHTML='') : void
	{
		$id = isset($options['id']) ? $options['id'] : HtmlSnippets::getRandomUUID();
		unset($options['id']);
		
		$opts = '';
		foreach($options as $opKey => $opVal)
		{
			$opKey=trim($opKey);
			if(
				strpos($opKey, "'") !== false or
				strpos($opKey, " ") !== false or
				strpos($opKey, "=") !== false or
				strpos($opVal, "'") !== false
			)
			{
				throw Exception("Illegal characters in options");
			}
			
			$opts .= " {$opKey}='{$opVal}'";
		}
		
		?><button id='<?= $id ?>'<?= $opts ?>><?= $innerHTML ?></button><?php
		?><script>
			const oneTimeButton = document.getElementById('<?= $id ?>');
			oneTimeButton.addEventListener('click', (event) => {
				event.preventDefault();
				oneTimeButton.form.submit();
				oneTimeButton.setAttribute('disabled', 'disabled');
				return false;
			}, true);
		</script><?php
	}
	
	public static function printLocationSelection_About(string $selectId, string $aboutDivClass = '') : void
	{
		$aboutId = "{$selectId}_about";
		?>
		<div id='<?= $aboutId ?>'>
			<noscript>
				<p>Your postcards will have a code selected. You will be able to change the location in case you are traveling.</p>
			</noscript>
		</div>
		<script type='text/javascript' defer='defer'>
			document.getElementById('<?= $selectId ?>').addEventListener('change', (event) => {
				console.log(event.target.value);
				
				document.getElementById('<?= $aboutId ?>').innerHTML = 
					'<p>Your postcards will have a code: '+event.target.value+'. You will be able to change the location in case you are traveling.</p>'+
					'<p>Read more about this location <a href=\'/location/'+event.target.value+'\'>here</a>.</p>';
			});
		</script>
		<?php
	}
	public static function printLocationSelection_CodeEntry(string $selectId, string $entryDivClass = '') : void
	{
		$codeentryId = "{$selectId}_codeentry";
		?>
		<div id='<?= $codeentryId ?>'>
			<noscript>
			</noscript>
		</div>
		<script type='text/javascript' defer='defer'>
			const select = document.getElementById('<?= $selectId ?>');
			const codeentryDiv = document.getElementById('<?= $codeentryId ?>');
			const codeentryInput = document.createElement('input');
			codeentryInput.setAttribute('type', 'text');
			codeentryInput.setAttribute('autocapitalize', 'autocapitalize');
			codeentryInput.setAttribute('placeholder', 'Code of location (if known)');
			codeentryDiv.appendChild(codeentryInput);
			codeentryInput.addEventListener('keydown', (event) => {
				let value= codeentryInput.value;
				value = value.trim();
				value = value.toUpperCase();
				console.log(event.key);
				if (event.key == "Enter" || event.key == ' ')
				{
					select.value=value;
					
					event.preventDefault();
				}
				else if(value.length === 4)
				{
					select.value=value;
				}
			});
		</script>
		<?php
	}
	
	public static function printLocationSelectOptionList(string $defaultLocation = 'SOL3') : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('SELECT `code`, `name` FROM `location_code`');
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$selected = $row['code']==$defaultLocation ? ' selected=\'selected\'' : '';
			echo "<option value='{$row['code']}'{$selected}>{$row['name']} [{$row['code']}]</option>";
		}
	}
	private static function printUserPoliteName(string $login, string $polite_name, bool $link=false) : void
	{
		if(!isset($polite_name) || empty($polite_name))
		{
			$polite_name = $login;
		}
		
		if($link)
		{
			echo "<a href='/user/{$login}'>{$polite_name}</a>";
		}
		else
		{
			echo $polite_name;
		}
	}
}