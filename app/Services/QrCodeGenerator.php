<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeGenerator
{
    /**
     * Hasilkan QR code sebagai string SVG (tanpa dependency imagick/GD).
     */
    public function svg(string $data, int $size = 300): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd,
        );

        return (new Writer($renderer))->writeString($data);
    }

    /**
     * Hasilkan QR code sebagai data URI (base64) untuk disematkan di <img>.
     */
    public function dataUri(string $data, int $size = 300): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode($this->svg($data, $size));
    }
}
