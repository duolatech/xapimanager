<?php

/**
* Author : smallchicken
* Time   : 2009年6月8日16:46:05
* mode 1 : 强制裁剪，生成图片严格按照需要，不足放大，超过裁剪，图片始终铺满
* mode 2 : 和1类似，但不足的时候 不放大 会产生补白，可以用png消除。
* mode 3 : 只缩放，不裁剪，保留全部图片信息，会产生补白，
* mode 4 : 只缩放，不裁剪，保留全部图片信息，生成图片大小为最终缩放后的图片有效信息的实际大小，不产生补白
* 默认补白为白色，如果要使补白成透明像素，请使用SaveAlpha()方法代替SaveImage()方法
*
* 调用方法：
*
* $ic=new ImageCrop('old.jpg','afterCrop.jpg');
* $ic->Crop(120,80,2);
* $ic->SaveImage();
*        //$ic->SaveAlpha();将补白变成透明像素保存
* $ic->destory();
*
*
*/
namespace App\tools;
class ImageCrop {
	var $sImage;
	var $dImage;
	var $src_file;
	var $dst_file;
	var $src_width;
	var $src_height;
	var $src_ext;
	var $src_type;
	public function __construct($src_file, $dst_file = '') {
		$this->src_file = $src_file;
		$this->dst_file = $dst_file;
		$this->LoadImage ();
	}
	public function SetSrcFile($src_file) {
		$this->src_file = $src_file;
	}
	public function SetDstFile($dst_file) {
		$this->dst_file = $dst_file;
	}
	public function LoadImage() {
		list ( $this->src_width, $this->src_height, $this->src_type ) = getimagesize ( $this->src_file );
		
		switch ($this->src_type) {
			case IMAGETYPE_JPEG :
				$this->sImage = imagecreatefromjpeg ( $this->src_file );
				$this->ext = 'jpg';
				break;
			case IMAGETYPE_PNG :
				$this->sImage = imagecreatefrompng ( $this->src_file );
				$this->ext = 'png';
				break;
			case IMAGETYPE_GIF :
				$this->sImage = imagecreatefromgif ( $this->src_file );
				$this->ext = 'gif';
				break;
			case IMAGETYPE_WBMP :
					$this->sImage = imagecreatefromwbmp ( $this->src_file );
					$this->ext = 'bmp';
					break;
			default :
				exit ();
		}
	}
	public function SaveImage($fileName = '') {
		$this->dst_file = $fileName ? $fileName : $this->dst_file;
		switch ($this->src_type) {
			case IMAGETYPE_JPEG :
				imagejpeg ( $this->dImage, $this->dst_file, 100 );
				break;
			case IMAGETYPE_PNG :
				imagepng ( $this->dImage, $this->dst_file );
				break;
			case IMAGETYPE_GIF :
				imagegif ( $this->dImage, $this->dst_file );
				break;
			case IMAGETYPE_WBMP :
				imagewbmp ( $this->dImage, $this->dst_file );
				break;
			default :
				break;
		}
	}
	public function OutImage() {
		switch ($this->src_type) {
			case IMAGETYPE_JPEG :
				header ( 'Content-type: image/jpeg' );
				imagejpeg ( $this->dImage );
				break;
			case IMAGETYPE_PNG :
				header ( 'Content-type: image/png' );
				imagepng ( $this->dImage );
				break;
			case IMAGETYPE_GIF :
				header ( 'Content-type: image/gif' );
				imagegif ( $this->dImage );
				break;
			case IMAGETYPE_WBMP :
				header ( 'Content-type: image/bmp' );
				imagewbmp ( $this->dImage );
				break;
			default :
				break;
		}
	}
	public function SaveAlpha($fileName = '') {
		$this->dst_file = $fileName ? $fileName . '.png' : $this->dst_file . '.png';
		imagesavealpha ( $this->dImage, true );
		imagepng ( $this->dImage, $this->dst_file );
	}
	public function OutAlpha() {
		imagesavealpha ( $this->dImage, true );
		header ( 'Content-type: image/png' );
		imagepng ( $this->dImage );
	}
	public function destory() {
		imagedestroy ( $this->sImage );
		imagedestroy ( $this->dImage );
	}
	public function Crop($dst_width, $dst_height, $mode = 1, $dst_file = '') {
		if ($dst_file)
			$this->dst_file = $dst_file;
		$this->dImage = imagecreatetruecolor ( $dst_width, $dst_height );
		
		$bg = imagecolorallocatealpha ( $this->dImage, 255, 255, 255, 127 );
		imagefill ( $this->dImage, 0, 0, $bg );
		imagecolortransparent ( $this->dImage, $bg );
		
		$ratio_w = 1.0 * $dst_width / $this->src_width;
		$ratio_h = 1.0 * $dst_height / $this->src_height;
		$ratio = 1.0;
		switch ($mode) {
			case 1 : // always crop
				if (($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) {
					$ratio = $ratio_w < $ratio_h ? $ratio_h : $ratio_w;
					$tmp_w = ( int ) ($dst_width / $ratio);
					$tmp_h = ( int ) ($dst_height / $ratio);
					$tmp_img = imagecreatetruecolor ( $tmp_w, $tmp_h );
					$src_x = ( int ) (($this->src_width - $tmp_w) / 2);
					$src_y = ( int ) (($this->src_height - $tmp_h) / 2);
					imagecopy ( $tmp_img, $this->sImage, 0, 0, $src_x, $src_y, $tmp_w, $tmp_h );
					imagecopyresampled ( $this->dImage, $tmp_img, 0, 0, 0, 0, $dst_width, $dst_height, $tmp_w, $tmp_h );
					imagedestroy ( $tmp_img );
				} else {
					$ratio = $ratio_w < $ratio_h ? $ratio_h : $ratio_w;
					$tmp_w = ( int ) ($this->src_width * $ratio);
					$tmp_h = ( int ) ($this->src_height * $ratio);
					$tmp_img = imagecreatetruecolor ( $tmp_w, $tmp_h );
					imagecopyresampled ( $tmp_img, $this->sImage, 0, 0, 0, 0, $tmp_w, $tmp_h, $this->src_width, $this->src_height );
					$src_x = ( int ) ($tmp_w - $dst_width) / 2;
					$src_y = ( int ) ($tmp_h - $dst_height) / 2;
					imagecopy ( $this->dImage, $tmp_img, 0, 0, $src_x, $src_y, $dst_width, $dst_height );
					imagedestroy ( $tmp_img );
				}
				break;
			case 2 : // only small
				if ($ratio_w < 1 && $ratio_h < 1) {
					$ratio = $ratio_w < $ratio_h ? $ratio_h : $ratio_w;
					$tmp_w = ( int ) ($dst_width / $ratio);
					$tmp_h = ( int ) ($dst_height / $ratio);
					$tmp_img = imagecreatetruecolor ( $tmp_w, $tmp_h );
					$src_x = ( int ) ($this->src_width - $tmp_w) / 2;
					$src_y = ( int ) ($this->src_height - $tmp_h) / 2;
					imagecopy ( $tmp_img, $this->sImage, 0, 0, $src_x, $src_y, $tmp_w, $tmp_h );
					imagecopyresampled ( $this->dImage, $tmp_img, 0, 0, 0, 0, $dst_width, $dst_height, $tmp_w, $tmp_h );
					imagedestroy ( $tmp_img );
				} elseif ($ratio_w > 1 && $ratio_h > 1) {
					$dst_x = ( int ) abs ( $dst_width - $this->src_width ) / 2;
					$dst_y = ( int ) abs ( $dst_height - $this->src_height ) / 2;
					imagecopy ( $this->dImage, $this->sImage, $dst_x, $dst_y, 0, 0, $this->src_width, $this->src_height );
				} else {
					$src_x = 0;
					$dst_x = 0;
					$src_y = 0;
					$dst_y = 0;
					if (($dst_width - $this->src_width) < 0) {
						$src_x = ( int ) ($this->src_width - $dst_width) / 2;
						$dst_x = 0;
					} else {
						$src_x = 0;
						$dst_x = ( int ) ($dst_width - $this->src_width) / 2;
					}
					
					if (($dst_height - $this->src_height) < 0) {
						$src_y = ( int ) ($this->src_height - $dst_height) / 2;
						$dst_y = 0;
					} else {
						$src_y = 0;
						$dst_y = ( int ) ($dst_height - $this->src_height) / 2;
					}
					imagecopy ( $this->dImage, $this->sImage, $dst_x, $dst_y, $src_x, $src_y, $this->src_width, $this->src_height );
				}
				break;
			case 3 : // keep all image size and create need size
				if ($ratio_w > 1 && $ratio_h > 1) {
					$dst_x = ( int ) (abs ( $dst_width - $this->src_width ) / 2);
					$dst_y = ( int ) (abs ( $dst_height - $this->src_height ) / 2);
					imagecopy ( $this->dImage, $this->sImage, $dst_x, $dst_y, 0, 0, $this->src_width, $this->src_height );
				} else {
					$ratio = $ratio_w > $ratio_h ? $ratio_h : $ratio_w;
					$tmp_w = ( int ) ($this->src_width * $ratio);
					$tmp_h = ( int ) ($this->src_height * $ratio);
					$tmp_img = imagecreatetruecolor ( $tmp_w, $tmp_h );
					imagecopyresampled ( $tmp_img, $this->sImage, 0, 0, 0, 0, $tmp_w, $tmp_h, $this->src_width, $this->src_height );
					$dst_x = ( int ) (abs ( $tmp_w - $dst_width ) / 2);
					$dst_y = ( int ) (abs ( $tmp_h - $dst_height ) / 2);
					imagecopy ( $this->dImage, $tmp_img, $dst_x, $dst_y, 0, 0, $tmp_w, $tmp_h );
					imagedestroy ( $tmp_img );
				}
				break;
			case 4 : // keep all image but create actually size
				if ($ratio_w > 1 && $ratio_h > 1) {
					$this->dImage = imagecreatetruecolor ( $this->src_width, $this->src_height );
					imagecopy ( $this->dImage, $this->sImage, 0, 0, 0, 0, $this->src_width, $this->src_height );
				} else {
					$ratio = $ratio_w > $ratio_h ? $ratio_h : $ratio_w;
					$tmp_w = ( int ) ($this->src_width * $ratio);
					$tmp_h = ( int ) ($this->src_height * $ratio);
					$this->dImage = imagecreatetruecolor ( $tmp_w, $tmp_h );
					imagecopyresampled ( $this->dImage, $this->sImage, 0, 0, 0, 0, $tmp_w, $tmp_h, $this->src_width, $this->src_height );
				}
				break;
		}
	} // end Crop
}
?>