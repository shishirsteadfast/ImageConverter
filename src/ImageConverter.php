<?php

namespace Jeishanul\ImageConverter;

use Imagick;
use ImagickException;
use Exception;

class ImageConverter
{
    private $supportedFormats = [
        'bmp', 'jpeg', 'jpg', 'png', 'gif', 'tiff', 'tif', 'webp', 'heic', 'heif',
        'psd', 'ico', 'svg', 'ai', 'eps', 'pdf', 'raw', 'cr2', 'nef', 'arw', 'dng',
        'rw2', 'orf', 'pef', 'srf', 'sr2', 'exr', 'dds', 'apng', 'jfif', 'avif'
    ];
    
    private $inputPath;
    private $outputPath;
    private $targetFormat;
    private $quality;

    public function __construct(string $inputPath, string $outputPath, string $targetFormat, int $quality = 90)
    {
        $this->inputPath = $inputPath;
        $this->outputPath = $outputPath;
        $this->setTargetFormat($targetFormat);
        $this->quality = max(0, min(100, $quality));
    }

    // New static method for simple conversion
    public static function convertSimple(string $inputFile, string $targetExtension): bool
    {
        if (!file_exists($inputFile)) {
            throw new Exception("Input file does not exist: {$inputFile}");
        }

        $targetExtension = strtolower(ltrim($targetExtension, '.'));
        $outputFile = pathinfo($inputFile, PATHINFO_FILENAME) . '.' . $targetExtension;
        $outputDir = pathinfo($inputFile, PATHINFO_DIRNAME);
        $outputPath = $outputDir . DIRECTORY_SEPARATOR . $outputFile;

        $converter = new self($inputFile, $outputPath, $targetExtension);
        return $converter->convert();
    }

    public function setTargetFormat(string $format): self
    {
        $format = strtolower($format);
        if (!in_array($format, $this->supportedFormats)) {
            throw new Exception("Unsupported target format: {$format}");
        }
        $this->targetFormat = $format;
        return $this;
    }

    public function setQuality(int $quality): self
    {
        $this->quality = max(0, min(100, $quality));
        return $this;
    }

    public function convert(): bool
    {
        if (!file_exists($this->inputPath)) {
            throw new Exception("Input file does not exist: {$this->inputPath}");
        }

        $extension = strtolower(pathinfo($this->inputPath, PATHINFO_EXTENSION));
        if (!in_array($extension, $this->supportedFormats)) {
            throw new Exception("Unsupported input format: {$extension}");
        }

        try {
            if (extension_loaded('imagick') && $this->isImagickSupported($extension)) {
                return $this->convertWithImagick();
            } elseif (extension_loaded('gd') && $this->isGdSupported($extension)) {
                return $this->convertWithGd();
            } else {
                throw new Exception("No suitable image processing extension available for format: {$extension}");
            }
        } catch (Exception $e) {
            throw new Exception("Conversion failed: " . $e->getMessage());
        }
    }

    private function isImagickSupported(string $extension): bool
    {
        return class_exists('Imagick') && in_array(strtoupper($extension), (new Imagick())->queryFormats());
    }

    private function isGdSupported(string $extension): bool
    {
        $gdFormats = ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'webp'];
        return in_array($extension, $gdFormats);
    }

    private function convertWithImagick(): bool
    {
        try {
            $imagick = new Imagick($this->inputPath);

            // Handle special cases for vector and RAW formats
            if (in_array(strtolower(pathinfo($this->inputPath, PATHINFO_EXTENSION)), ['svg', 'ai', 'eps', 'pdf'])) {
                $imagick->setImageBackgroundColor('white');
                $imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
                $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
            }

            // Set compression quality
            $imagick->setImageCompressionQuality($this->quality);

            // Convert to target format
            $imagick->setImageFormat($this->targetFormat);

            // Optimize for specific formats
            if ($this->targetFormat === 'jpeg' || $this->targetFormat === 'jpg') {
                $imagick->setInterlaceScheme(Imagick::INTERLACE_JPEG);
                $imagick->stripImage(); // Remove metadata to reduce file size
            } elseif ($this->targetFormat === 'png') {
                $imagick->setOption('png:compression-level', '9');
            } elseif ($this->targetFormat === 'webp') {
                $imagick->setOption('webp:lossless', $this->quality === 100 ? 'true' : 'false');
            }

            // Write to output file
            return $imagick->writeImage($this->outputPath);
        } catch (ImagickException $e) {
            throw new Exception("Imagick conversion failed: " . $e->getMessage());
        }
    }

    private function convertWithGd(): bool
    {
        $extension = strtolower(pathinfo($this->inputPath, PATHINFO_EXTENSION));
        $targetExtension = $this->targetFormat;

        // Load image based on input format
        $image = null;
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($this->inputPath);
                break;
            case 'png':
                $image = imagecreatefrompng($this->inputPath);
                break;
            case 'gif':
                $image = imagecreatefromgif($this->inputPath);
                break;
            case 'bmp':
                $image = imagecreatefrombmp($this->inputPath);
                break;
            case 'webp':
                $image = imagecreatefromwebp($this->inputPath);
                break;
            default:
                throw new Exception("GD does not support input format: {$extension}");
        }

        if (!$image) {
            throw new Exception("Failed to load image with GD");
        }

        // Save image in target format
        $result = false;
        switch ($targetExtension) {
            case 'jpeg':
            case 'jpg':
                $result = imagejpeg($image, $this->outputPath, $this->quality);
                break;
            case 'png':
                $compression = (int) (9 - ($this->quality / 100) * 9);
                $result = imagepng($image, $this->outputPath, $compression);
                break;
            case 'gif':
                $result = imagegif($image, $this->outputPath);
                break;
            case 'bmp':
                $result = imagebmp($image, $this->outputPath);
                break;
            case 'webp':
                $result = imagewebp($image, $this->outputPath, $this->quality);
                break;
            default:
                throw new Exception("GD does not support output format: {$targetExtension}");
        }

        imagedestroy($image);
        return $result;
    }
}