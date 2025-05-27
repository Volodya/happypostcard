<?php

class Picture implements Image
{
	protected string $hash;
	protected string $ext;
	
	const dirThumbs  = 'uploads';
	const dirUploads = 'uploads';
	
	public static function makeThumbs(Imagick $image, array $boxes) : void // $boxes are [ [ 200, 200, '/path200/file.webp' ], [ 640, 480, '/path/other/file.webp' ], ]
	{
		$cW = $image->getImageWidth();
		$cH = $image->getImageHeight();
		
		foreach($boxes as $box)
		{
			$thumb = clone $image;
			$thumb->setImageFormat('webp');
			$thumb->stripImage();
			
			$nW = $box[0];
			$nH = $box[1];
			$thumbPath = $box[2];
			
			if($nW >= $cW AND $nH >= $cH) // We do not upscale
			{
				
			}
			else
			{
				$rW = $nW / $cW;
				$rH = $nH / $cH;
				
				$ratio = min($rW, $rH);
				
				$nW = intval($cW * $ratio);
				$nH = intval($cH * $ratio);
				
				$thumb->resizeImage($nW, $nH, Imagick::FILTER_LANCZOS, 1);
			}
			$thumb->writeImage($thumbPath);
			$thumb->destroy();
		}
	}
	
	protected function __construct()
	{
	}
	
	public function getHash() : string
	{
		return $this->hash;
	}
	public function getExt() : string
	{
		return $this->ext;
	}
	public function getThumb200() : string
	{
		return '/' . Picture::dirThumbs . "/200thumbs/{$this->hash}.{$this->ext}";
	}
	public function getThumb800() : string
	{
		return '/' . Picture::dirThumbs . "/800thumbs/{$this->hash}.{$this->ext}";
	}
	public function getPictureUrl() : string
	{
		return "/image/{$this->hash}";
	}
	public function getFileUrl() : string
	{
		// probably incorrect regarding extension
		return '/' . Picture::dirUploads . "/{$this->hash}.{$this->ext}";
	}
}
