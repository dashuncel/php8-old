<?php
class Gd 
{
	private $width;
	private $height;
	private $text;
	private $color;

	public function __construct($width, $height, $text) {
		$this->width  = $width;
		$this->height = $height;
		$this->text   = $text;
	}

	public function generate() {
        // изменение размеров картинки под заданные размеры в объекте: 
        $destImg   = imagecreatetruecolor($this->width, $this->height);
        $srcImg    = imageCreateFromJpeg(__DIR__.DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."ramka.jpg");
        $srcWidth  = ImageSX($srcImg);
        $srcHeight = ImageSY($srcImg);
        $res=ImageCopyResampled($destImg, $srcImg, 0, 0, 0, 0, $this->width, $this->height, $srcWidth, $srcHeight);

        $font=__DIR__.DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."albionic.ttf";
        $textColor=imagecolorallocate($destImg, 123, 104, 238);

        // вычисляем размер щрифта:
        // позиционирование текста в рамке:  
        $txt=explode('&', $this->text);
        $maxlen=0;
        foreach ($txt as $piece) { 
          $maxlen=max($maxlen, strlen($piece));
        }
        $px = round (($this->width - $maxlen) / $maxlen, 0); // размер шрифта
        $pos_y = round(($this->height - ($px + 10) * count($txt)) / 2, 0);      // позиция по вертикали
        foreach ($txt as $piece) {
          $pos_x = round(($this->width - $px / 2 * mb_strlen($piece)) / 2, 0); // позиция по горизонтали
          imagettftext($destImg, $px, 0, $pos_x, $pos_y, $textColor, $font, $piece);
         /* echo $piece.'-'.$pos_x.'-'.$pos_y.'-'.$px.'-'.$this->width.'-'.mb_strlen($piece).'<br/>';*/
          $pos_y += $px + 10;
        } 

        /*--маленький штампик--*/
        $stampW = round($this->width / 6, 0);
        $stampH = round($this->width / 9, 0);

        $stamp = imagecreatetruecolor($stampW, $stampH);
        $stampSrc = imageCreateFromPng(__DIR__.DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."stamp.png");
        $res=ImageCopyResampled($stamp, $stampSrc, 0, 0, 0, 0, $stampW , $stampH , ImageSX($stampSrc), ImageSX($stampSrc));
        imagealphablending($stamp, false);
        imagesavealpha($stamp, true);

        $pos_x = $this->width -  2 * $stampW; // позиция по горизонтали
        $pos_y = $this->height - 2 * $stampH; // позиция по горизонтали

        imagecopy($destImg, $stamp, $pos_x, $pos_y, 0,0, imagesx($stamp), ImageSY($stamp));
        header('Content-type: image/png');
        imagePng($destImg);
        imagedestroy($destImg);
	}
}
