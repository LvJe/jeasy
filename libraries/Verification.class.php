<?php
/**
 * Class Authority
 *
 * Author   JE.Xie
 * Date     2017.07.11
 *
 */

class Verification
{
    /**
     * 生成随机数字串
     * @param int $length
     * @return string
     */
    public static function generateNumbers($length = 6){
        $code = '';
        for ($i = 0; $i<$length; $i++)
            $code .= rand(0, 9);
        return $code;
    }

    /**
     * 生成随机字符串（只包含大写字母）
     * @param int $length
     * @return string
     */
    public static function generateLetters($length = 6){
        $code = '';
        for ($i = 0; $i<$length; $i++)
            $code .= chr(rand(ord('A'), ord('Z')));
        return $code;
    }

    /**
     * 生成验证码图片
     * @param $code
     * @param int $width
     * @param int $height
     */
    public static function createImage( $code, $width = 100, $height = 40){
        ob_end_clean();

        header("Content-type: image/png");
        $image = imagecreate($width, $height);

        $fontColor = imagecolorallocate($image, 10, 50, 100);
        $dotColor = imagecolorallocate($image, 100, 80, 150);
        $bgColor = imagecolorallocate($image, 200, 200, 200);

        imagefill($image, 0, 0, $bgColor);

        $style = array($dotColor, $dotColor, $dotColor, $dotColor, $bgColor, $bgColor, $bgColor, $bgColor);
        imagesetstyle($image, $style);
        imageline($image, 0, rand(0, $height), $width, rand(0, $height), IMG_COLOR_STYLED);
        //imageline($image, 0, rand(0, $height), $width, rand(0, $height), IMG_COLOR_STYLED);
        imagearc($image, rand(0, $width*2), rand(0, $height*2), rand(10, $width*2), rand(10, $height*2), 0, 360, $dotColor);

        $dotCount = $width * $height / 10;
        for ($i = 0; $i<$dotCount; $i++)
            imagesetpixel($image, rand(0, $width), rand(0, $height), $dotColor);

        $len = strlen($code);
        $wid = $width / $len;
        for ($i = 0; $i<$len; $i++) {
            $strX = $wid * $i + rand(2, $wid/2);
            $strY = rand($height/5, $height/2);
            imagestring($image, 5, $strX, $strY, substr($code, $i, 1), $fontColor);
        }

        imagepng($image);
        imagedestroy($image);

        exit;
    }

}