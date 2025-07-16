<?php

class SvgGraphLine
{
	private array $points;
	private float $min;
	private float $max;
	
	private string $name;
	private string $colour;
	
	public function __construct(string $name)
	{
		$this->points = [];
		$this->min = 0;
		$this->max = 0;
		
		$this->name = htmlentities($name);
		$this->colour = 'black';
	}
	
	public function setColour(string $colour)
	{
		$this->colour = $colour;
	}
	
	public function addPoint(float $y) : void
	{
		$this->points[] = $y;
		
		$this->min = min($this->min, $y);
		$this->max = max($this->max, $y);
	}
	
	// +1 = up, 0 = horizontal, -1 = down
	private static function getDirection(float $yFrom, float $yTo) : int
	{
		return ( $yFrom - $yTo ) <=> 0;
	}
	
	private function printLine(float $xUnit, float $yUnit, int $xFrom, float $yFrom, float $yTo) : void
	{
		$xTo = $xFrom + 1;
		
		$x1 =   $xFrom * $xUnit;
		$y1 = - $yFrom * $yUnit;
		$x2 =   $xTo * $xUnit;
		$y2 = - $yTo * $yUnit;
		
		?><line x1="<?= $x1 ?>" y1="<?= $y1 ?>" x2="<?= $x2 ?>" y2="<?= $y2 ?>" stroke="<?= $this->colour ?>" stroke-width="1" /><?php
	}
	private static function printLabel(
		float $xUnit, float $yUnit, float $fontSize, float $labelsLeftShift,
		int $x, float $y, bool $above, string $label
	) : void
	{
		?><text x="<?= $x * $xUnit - $labelsLeftShift ?>" y="-<?= $y * $yUnit + ($above ? -$fontSize : $fontSize) ?>" <?php
			?>font-size="<?= $fontSize ?>"><?= $label ?></text><?php
	}
	public function printLineAndLabels(float $xUnit, float $yUnit, float $fontSize, float $labelsLeftShift) : void
	{
		if(empty($this->points)) return;
		
		$prevDir = 0;
		$prev = $this->points[0];
		
		for($i=1; $i < count($this->points); ++$i)
		{
			$cur = $this->points[$i];
			
			$dir = self::getDirection($prev, $cur);
			
			$this->printLine($xUnit, $yUnit, $i, $prev, $cur);
			if(($i==1 or $dir * $prevDir == -1) and $prev > 0) // if they are different
			{
				self::printLabel($xUnit, $yUnit, $fontSize, $labelsLeftShift, $i, $prev, $dir < 0, strval($prev));
			}
			
			$prev = $cur;
			$prevDir = $dir;
		}
		self::printLabel($xUnit, $yUnit, $fontSize, $labelsLeftShift, count($this->points), $prev, $dir > 0, strval($prev));
	}
	public function printLegend(float $x, float $y, float $fontSize) : void
	{
		?><text <?php
			?>x="<?= $x ?>" y="<?= $y ?>" <?php
			?>font-size="<?= $fontSize ?>" <?php
			?>fill="<?= $this->colour ?>"><?php
			
			echo $this->name;
			
		?></text><?php
	}
	public function getNumOfPoints() : int
	{
		return count($this->points);
	}
	public function getMin() : int
	{
		return $this->min;
	}
	public function getMax() : int
	{
		return $this->max;
	}
}