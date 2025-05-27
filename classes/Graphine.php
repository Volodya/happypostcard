<?php

class Graphine implements Image
{
	private string $hash;
	private int $userId;
	
	const dir = 'graphines';
	const ext = 'webp';
	
	public function __construct(int $userId, string $userLogin)
	{
		$this->hash = hash('sha256', $userLogin, false);
		$this->userId = $userId;
	}
	
	public static function constructByUser(UserExisting $user) : Graphine
	{
		return new Graphine($user->getId(), $user->getLogin());
	}
	public function getHash() : string
	{
		return $this->hash;
	}
	public function getExt() : string
	{
		return self::ext;
	}
	
	public function getFileUrl() : string
	{
		return $this->getThumb200();
	}
	public function getThumb200() : string
	{
		$fileName = 
			'/' . Graphine::dir . '/' .
			$this->hash . '.' . Graphine::ext;
		if(!file_exists($fileName))
		{
			$localFileName =
				Config::getInstance()->getPropertyOrThrow('rootdir', new Exception()) .
				$fileName;
			Graphine::generate($this->hash, $localFileName);
		}
		return $fileName;
	}
	
		/*
		 * 8 colours
		 * 16 points
		 * 
		 * 6 bits = x
		 * 6 bits = y
		 * 
		 * 8 bit colour (64 variants)
		**/
	private static function processHash(string $hash) : array
	{
		$hash = str_pad(Graphine::base16to2($hash), 256, '0', STR_PAD_LEFT);
		
		$hash = str_split($hash, 192); // first 192 bits are points, 64 bits are for colours
		
		$gData = [];
		
		$gData['points'] = str_split($hash[0], 12); // each point is 12 bits
		$gData['points'] = array_map(fn($val) : array => str_split($val, 6), $gData['points']); // each point is x and y value
		
		$gData['points'] = array_chunk($gData['points'], 4, false); // four points per quarter
		
		$gData['colours'] = str_split($hash[1], 8); // 8 coloured regions
		$gData['colours'] = array_map(fn($val) : array => str_split($val, 3), $gData['colours']); // r: 3 g: 3 b: 2
		
		
		$gData = Graphine::array_map_recursive(fn($bits) : int => intval($bits, 2), $gData);
		
		$gData['points'] = array_map('Graphine::padPoints', $gData['points']);
		$gData['points'] = array_map('Graphine::sortPoints', $gData['points']);
		$gData['points'] = array_map('Graphine::placePointsInQuarter', $gData['points'], array_keys($gData['points']));
		
		$gData['colours'] = array_map(function($c) : \ImagickPixel {
				$r = ($c[0] << 5);
				$g = ($c[1] << 5);
				$b = ($c[2] << 6);
				return new ImagickPixel("rgb({$r}, {$g}, {$b})");
			}, $gData['colours']);
		
		return $gData;
	}
	private static function makeDraw(array $gData) : \ImagickDraw
	{
		$draw = new \ImagickDraw();
		$draw->setStrokeColor( new \ImagickPixel( 'black' ) );
		foreach(range(0, 3) as $qrt)
		{
			if(
				Graphine::linesIntersect(
					$gData['points'][$qrt][0], $gData['points'][$qrt][1],
					$gData['points'][$qrt][3], $gData['points'][$qrt][2]
				)
				||
				Graphine::linesIntersect(
					$gData['points'][$qrt][0], $gData['points'][$qrt][2],
					$gData['points'][$qrt][3], $gData['points'][$qrt][1]
				)
			)
			{
				// connect to centre
				$draw->setFillColor( $gData['colours'][$qrt] );
				$draw->pathStart();
				$draw->pathMoveToAbsolute(100, 100);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][0][0], $gData['points'][$qrt][0][1]);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][3][0], $gData['points'][$qrt][3][1]);
				$draw->pathClose();
				$draw->pathFinish();
				
				// draw "tropezoid"
				$draw->setFillColor( $gData['colours'][$qrt+4] );
				$draw->pathStart();
				$draw->pathMoveToAbsolute($gData['points'][$qrt][0][0], $gData['points'][$qrt][0][1]);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][1][0], $gData['points'][$qrt][1][1]);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][2][0], $gData['points'][$qrt][2][1]);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][3][0], $gData['points'][$qrt][3][1]);
				$draw->pathClose();
				$draw->pathFinish();
			}
			else
			{
				// connect to centre
				$draw->setFillColor( $gData['colours'][$qrt] );
				$draw->pathStart();
				$draw->pathMoveToAbsolute(100, 100);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][0][0], $gData['points'][$qrt][0][1]);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][1][0], $gData['points'][$qrt][1][1]); // 2 would also work
				$draw->pathMoveToAbsolute($gData['points'][$qrt][3][0], $gData['points'][$qrt][3][1]);
				$draw->pathClose();
				$draw->pathFinish();
				
				// draw "rhombus" or "arror"
				$draw->setFillColor( $gData['colours'][$qrt+4] );
				$draw->pathStart();
				$draw->pathMoveToAbsolute($gData['points'][$qrt][0][0], $gData['points'][$qrt][0][1]);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][1][0], $gData['points'][$qrt][1][1]);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][3][0], $gData['points'][$qrt][3][1]);
				$draw->pathMoveToAbsolute($gData['points'][$qrt][2][0], $gData['points'][$qrt][2][1]);
				$draw->pathClose();
				$draw->pathFinish();
			}
			// connect previous
			$pqrt = ($qrt + 3) % 4;
			$draw->setFillColor( $gData['colours'][$pqrt+4] );
			$draw->pathStart();
			$draw->pathMoveToAbsolute(100, 100);
			$draw->pathMoveToAbsolute($gData['points'][$qrt][3][0], $gData['points'][$qrt][3][1]);
			$draw->pathMoveToAbsolute($gData['points'][$pqrt][0][0], $gData['points'][$pqrt][0][1]);
			$draw->pathClose();
			$draw->pathFinish();
		}
		
		return $draw;
	}
	private static function generateMask(int $w, int $h, int $b) : \Imagick
	{
		$mask = new \Imagick();
		$mask->newPseudoImage($w, $w, 'canvas:transparent');
		$draw = new \ImagickDraw();
		$draw->setStrokeColor( new ImagickPixel( 'black' ) );
		$draw->setFillColor( new ImagickPixel( 'black' ) );
		$draw->roundRectangle($b, $b, $w-$b*2, $w-$b*2, $b*2, $b*2);
		$mask->drawImage( $draw );
		$mask->blurImage($b*2, $b);
		
		return $mask;
	}
	private static function generate(string $hash, string $fileName) : void
	{
		$gData = Graphine::processHash($hash);
		
		$draw = Graphine::makeDraw($gData);
		
		$canvas = new Imagick();
		$canvas->newImage(200, 200, "white");
		$canvas->drawImage( $draw );
		
		$mask = Graphine::generateMask(200, 200, 10);
		
		$canvas->compositeImage($mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);
		
		$canvas->setImageFormat('webp');
		$canvas->writeImage($fileName);
		$canvas->destroy();
	}
	
	// because base_convert does not handle large number
	private static function base16to2(string $num) : string
	{
		$num = strtoupper($num);
		$hexBits = [
			'0' => '0000', '1' => '0001', '2' => '0010', '3' => '0011',
			'4' => '0100', '5' => '0101', '6' => '0110', '7' => '0111',
			'8' => '1000', '9' => '1001', 'A' => '1010', 'B' => '1011',
			'C' => '1100', 'D' => '1101', 'E' => '1110', 'F' => '1111',
		];
		$result = '';
		foreach(str_split($num) as $d)
		{
			$result .= $hexBits[$d];
		}
		return $result;
	}

	// square is 100x100, 26 < x <= 90, 26 < y <= 90
	private static function padPoints(array $points) : array
	{
		$result = [];
		foreach($points as $p)
		{
			$result[] = [$p[0] + 27, $p[1] + 27];
		}
		return $result;
	}

	// inspired by https://stackoverflow.com/a/51555878/2893496
	private static function sortPoints(array $points) : array
	{
		$getTheta = function(int $x, int $y) : float
		{
			return atan2($y, $x);
		};
		
		$angles = array_map(fn(array $p) : float => $getTheta($p[0], $p[1]), $points);
		asort($angles);
		
		$result = [];
		foreach($angles as $key => $val)
		{
			$result[] = $points[$key];
		}
		return $result;
	}
	
	private static function placePointsInQuarter(array $points, int $q) : array
	{
		if($q == 0)
		{
			$pPlace = fn($p) : array => [ $p[0] + 100, $p[1] + 100 ];
		}
		else if($q == 1)
		{
			$pPlace = fn($p) : array => [ $p[1] + 100, -$p[0] + 100 ];
		}
		else if($q == 2)
		{
			$pPlace = fn($p) : array => [ -$p[0] + 100, -$p[1] + 100 ];
		}
		else if($q == 3)
		{
			$pPlace = fn($p) : array => [ -$p[1] + 100, $p[0] + 100 ];
		}
		else
		{
			throw new Exception("wrong quarter {$q}");
		}
		return array_map($pPlace, $points);
	}
	
	//https://stackoverflow.com/a/39637749/2893496
	private static function array_map_recursive($callback, $array)
	{
		$func = function ($item) use (&$func, &$callback) {
			return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
		};
		
		return array_map($func, $array);
	}
	
	private static function vectorMinus(array $v1, array $v2) : array
	{
		return array_map(fn(float $val1, float $val2) : float => $val1-$val2, $v1, $v2);
	}
	private static function vectorDot(array $v1, array $v2) : float
	{
		return array_sum(array_map(fn(float $val1, float $val2) : float => $val1*$val2, $v1, $v2));
	}
	
	private static function linesIntersect($l1Start, $l1End, $l2Start, $l2End) : bool
	{
		// https://stackoverflow.com/a/563275/2893496
		$a = $l1Start;
		$b = $l1End;
		$c = $l2Start;
		$d = $l2End;
		
		$e = Graphine::vectorMinus($b, $a);
		$f = Graphine::vectorMinus($d, $c);
		$p = [ -$e[1], $e[0] ];
		$denom = Graphine::vectorDot($f, $p);
		if($denom == 0) return false;
		$numer = Graphine::vectorDot( Graphine::vectorMinus($a, $c), $p );
		if($numer == 0) return false;
		$h = abs( $numer / $denom );
		return $h < 1;
	}
}
