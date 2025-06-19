<?php

class SvgGraph
{
	private array $points;
	private float $min;
	private float $max;
	
	private float $xUnit;
	private float $yUnit;
	private float $fontSize;
	private float $labelsLeftShift;
	
	public function __construct()
	{
		$this->points = [];
		$this->min = 0;
		$this->max = 0;
		
		$this->xUnit = 10;
		$this->yUnit = 3;
		$this->fontSize = 8;
		$this->labelsLeftShift = 4;
	}
	
	public function addPoint(float $y) : void
	{
		$this->points[] = $y;
		
		$this->min = min($this->min, $y);
		$this->max = max($this->max, $y);
	}
	
	// +1 = up, 0 = horizontal, -1 = down
	private function getDirection(float $yFrom, float $yTo) : int
	{
		return ( $yFrom - $yTo ) <=> 0;
	}
	
	private function printLine(int $xFrom, float $yFrom, float $yTo) : void
	{
		$xTo = $xFrom + 1;
		
		$x1 =   $xFrom * $this->xUnit;
		$y1 = - $yFrom * $this->yUnit;
		$x2 =   $xTo * $this->xUnit;
		$y2 = - $yTo * $this->yUnit;
		
		?><line x1="<?= $x1 ?>" y1="<?= $y1 ?>" x2="<?= $x2 ?>" y2="<?= $y2 ?>" stroke="blue" stroke-width="1" /><?php
	}
	private function printLabel(int $x, float $y, bool $above, string $label) : void
	{
		?><text x="<?= $x * $this->xUnit - $this->labelsLeftShift ?>" y="-<?= $y * $this->yUnit + ($above ? -$this->fontSize : $this->fontSize) ?>"
			font-size="<?= $this->fontSize ?>"><?= $label ?></text><?php
	}
	private function printLinesAndLabels() : void
	{
		if(empty($this->points)) return;
		
		$prevDir = 0;
		$prev = $this->points[0];
		
		for($i=1; $i < count($this->points); ++$i)
		{
			$cur = $this->points[$i];
			
			$dir = $this->getDirection($prev, $cur);
			
			$this->printLine($i, $prev, $cur);
			if(($i==1 or $dir * $prevDir == -1) and $prev > 0) // if they are different
			{
				$this->printLabel($i, $prev, $dir < 0, strval($prev));
			}
			
			$prev = $cur;
			$prevDir = $dir;
		}
		$this->printLabel(count($this->points), $prev, $dir > 0, strval($prev));
	}
	
	private function printAxis() : void
	{
		?><rect fill='#fff' stroke='#000'
			x='-<?= $this->getMargin() ?>' y='-<?= $this->getHeight() + $this->getMargin() ?>'
			width='<?= $this->getWidth() + $this->getMargin()*2 ?>' height='<?= $this->getHeight() + $this->getMargin()*2 ?>'/>
		<line x1="0" y1="-<?= -2 + $this->getHeight() + $this->getMargin() ?>" x2="0" y2="3" stroke="black" stroke-width="1" />
		<line x1="<?= 2 - $this->getMargin() ?>" y1="0" x2="<?= $this->getWidth() ?>" y2="0" stroke="black" stroke-width="1" /><?php
	}
	private function getMargin() : int
	{
		return 5;
	}
	private function getWidth() : int
	{
		return (count($this->points) + 1) * $this->xUnit;
	}
	private function getHeight() : int
	{
		return $this->max * $this->yUnit + $this->fontSize*2;
	}
	private function printViewBox() : void
	{
		/* min-x */
		echo - $this->getMargin();
		echo ' ';
		/* min-y */
		echo - $this->getHeight() - $this->getMargin();
		echo ' ';
		/* width */
		echo   $this->getWidth() + $this->getMargin();
		echo ' ';
		/* height */
		echo $this->getHeight() + $this->getMargin() * 2;
	}
	
	public function print() : void
	{
		?><svg width='<?= $this->getWidth() + 10 ?>' height='<?= $this->getHeight() + 10 ?>'
			viewBox='<?php $this->printViewBox() ?>'
			xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><?php
		$this->printAxis();
		$this->printLinesAndLabels();
		?></svg><?php
	}
}