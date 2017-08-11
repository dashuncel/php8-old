<?php

class Captcha {

    const WIDTH = 150;
    const HEIGHT = 60;
    const FONT_SIZE = 16; // базовый размер шрифта
    const BG_LENGTH = 50; // количество буков на фоне
    const BG_LINES = 10; // количество линий "шум"
    const FONT = 'fonts/times.ttf';

    private static $letters = 'abdefghirstyz123456789';
    private static $colors  = ['40', '70', '100', '130', '160', '190', '210', '220', '115', '55'];

    public static function generate() {
        if (!session_id()) session_start();

        $length = mt_rand(5, 7); // длина капчи
        $numChars = strlen(self::$letters);
        $src = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        $bg = imagecolorallocate($src, 250, 250, 250);
        imageFill($src, 0, 0, $bg);

        // фон:
        for ($i = 0; $i < self::BG_LENGTH; $i++) {
            $color = imagecolorallocatealpha($src, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 100);
            $letter = substr(self::$letters, mt_rand(1, $numChars), 1); // очередной случайный символ
            $size = mt_rand(self::FONT_SIZE - 10, self::FONT_SIZE - 2);
            imagettftext($src, $size, mt_rand(0, 45), mt_rand(self::WIDTH * 0.1, self::WIDTH * 0.9),
                mt_rand(self::HEIGHT * 0.1, self::HEIGHT * 0.9), $color, self::FONT, $letter);
        }

        // шум в темных тонах:
        for ($i = 0; $i < self::BG_LINES; $i++) {
            $color = imagecolorallocatealpha($src, mt_rand(0, 10), mt_rand(0, 10), mt_rand(0, 10), 90);
            imageline($src, mt_rand(0, self::WIDTH), mt_rand(0, self::HEIGHT) , mt_rand(0, self::WIDTH), mt_rand(0, self::HEIGHT), $color);
        }

        // символы:
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $color = imagecolorallocatealpha($src, self::$colors[mt_rand(0, count(self::$colors) - 1)],
                self::$colors[mt_rand(0, count(self::$colors) - 1)],
                self::$colors[mt_rand(0, count(self::$colors) - 1)], mt_rand(20, 40));

            $letter = substr(self::$letters, mt_rand(1, $numChars), 1); // очередной случайный символ
            $size = mt_rand(self::FONT_SIZE * 2 - 2, self::FONT_SIZE * 2 + 2);
            $x = ($i + 1) * self::FONT_SIZE  + mt_rand(1, 5);
            $y = self::HEIGHT * 2  / 3 + mt_rand(1, 5);
            $code .= $letter;
            imagettftext($src, $size, mt_rand(-20, 20), $x, $y, $color, self::FONT, $letter);
        }

        $_SESSION['code'] = $code;
        header('Content-type: image/gif');
        imagegif($src);
    }

    public static function check($code) {
        if (!session_id()) session_start();
        return $code === $_SESSION['code'];
    }

}
