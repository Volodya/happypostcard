<?php

class ComWid_location_statistics implements ComWid
{
	private string $locationCode;
	
	private bool $displayed;
	
	public function __construct()
	{
		$this->displayed = false;
	}
	public function setEditor(User $editor) : void
	{}
	public function setTemplateParameter(array $parameter) : void
	{
		$this->locationCode = $parameter['location_code'];
	}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		$userCounts = Location::getUserCounts($this->locationCode);
		$postcardCounts = Location::getPostcardCounts($this->locationCode);
		
		?>
		<div>
			Total users with this location set: <?= $userCounts['now'] ?>
		</div>
		<div>
			Total users who ever sent from this location: <?= $userCounts['ever_from'] ?>
		</div>
		<div>
			Total users who ever had cards sent to them at this location: <?= $userCounts['ever_to'] ?>
		</div>
		<div>
			Total postcards sent from this location: <?= $postcardCounts['sent_from'] ?>
		</div>
		<div>
			Total postcards sent to this location: <?= $postcardCounts['sent_to'] ?>
		</div>
		<?php
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}