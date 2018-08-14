<?php

namespace App\Service;
use App\Entity\Image;
use App\HTTP\File\Base64EncodedFile;
use App\HTTP\File\UploadedBase64EncodedFile;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;

class QrCodeService
{
    /**
     * @param $code
     * @return Image
     */
    public function makeQrCode($code)
    {
        $qrCode = new QrCode($code);
        $qrCode->setSize(300);

        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setLogoWidth(150);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);

        $image = new Image();
        $file = base64_encode($qrCode->writeString());
        $file = new UploadedBase64EncodedFile(new Base64EncodedFile($file, true));
        $image->setImage($file);

        return $image;
    }
}
