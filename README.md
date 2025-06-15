Jeishanul Image Converter
A PHP Composer package to convert images between various formats while maintaining quality.
Installation
Install the package via Composer:
composer require jeishanul/image-converter

Ensure you have the imagick and gd PHP extensions installed. For advanced format support (e.g., HEIC, RAW, PDF), imagick is required.
Supported Formats

Input: BMP, JPEG, JPG, PNG, GIF, TIFF, TIF, WebP, HEIC, HEIF, PSD, ICO, SVG, AI, EPS, PDF, RAW, CR2, NEF, ARW, DNG, RW2, ORF, PEF, SRF, SR2, EXR, DDS, APNG, JFIF, AVIF
Output: Same as input formats (where supported by Imagick or GD)

Usage
Simple Conversion
Convert an image by passing only the input file and target extension. The output file is saved in the same directory with the same name but the new extension.
use Jeishanul\ImageConverter\ImageConverter;

try {
    ImageConverter::convertSimple('input/sample.png', 'jpg');
    echo "Image converted successfully to sample.jpg!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

Advanced Conversion
For more control, specify the output path and quality:
use Jeishanul\ImageConverter\ImageConverter;

try {
    $converter = new ImageConverter(
        'input/sample.png',       // Input file path
        'output/sample.jpg',      // Output file path
        'jpg',                   // Target format
        85                       // Quality (0-100)
    );

    $converter->convert();
    echo "Image converted successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

Requirements

PHP 7.4 or 8.0+
ext-imagick (recommended for broad format support)
ext-gd (fallback for basic formats: JPEG, PNG, GIF, BMP, WebP)

Notes

Imagick is used as the primary engine for its extensive format support, including vector and RAW formats.
GD is used as a fallback for basic formats if Imagick is unavailable or unsupported for a specific format.
Vector formats (SVG, AI, EPS, PDF) are rasterized during conversion, with a white background applied.
Quality settings (0-100) affect lossy formats like JPEG and WebP. For lossless formats like PNG, quality influences compression levels.
Ensure your system has necessary dependencies for advanced formats (e.g., Ghostscript for PDF, libheif for HEIC).

License
MIT
