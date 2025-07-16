<?php

class SvgGraph
{
	private array $lines; // array of SvgGraphLine
	
	private float $xUnit;
	private float $yUnit;
	private float $fontSize;
	private float $labelsLeftShift;
	
	public function __construct()
	{
		$this->lines = [];
		
		$this->xUnit = 10;
		$this->yUnit = 3;
		$this->fontSize = 8;
		$this->labelsLeftShift = 4;
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
		$numOfPoints = array_reduce(
			$this->lines,
			function($result, $item) {
				return max($result, $item->getNumOfPoints());
			},
			0
		);
		return ($numOfPoints + 1) * $this->xUnit;
	}
	private function getHeight() : int
	{
		$max = array_reduce(
			$this->lines,
			function($result, $item) {
				return max($result, $item->getMax());
			},
			0
		);
		return $max * $this->yUnit + $this->fontSize*2;
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
	
	public function addLine(SvgGraphLine $line)
	{
		$this->lines[] = $line;
	}
	private function printLegend() : void
	{
		$x = 10;
		$y = -(count($this->lines) * $this->fontSize);
		foreach($this->lines as $line)
		{
			$line->printLegend($x, $y, $this->fontSize);
			$y += $this->fontSize;
		}
	}
	public function print() : void
	{
		?><svg width='<?= $this->getWidth() + 10 ?>' height='<?= $this->getHeight() + 10 ?>'
			viewBox='<?php $this->printViewBox() ?>'
			xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><?php
		$this->printAxis();
		foreach($this->lines as $line)
		{
			$line->printLineAndLabels(
				$this->xUnit, $this->yUnit,
				$this->fontSize, $this->labelsLeftShift
			);
		}
		$this->printLegend();
		?></svg><?php
	}
}