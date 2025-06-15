# Jeishanul Image Converter

A PHP Composer package to convert images between various formats while maintaining quality.

---

## 🚀 Installation

Install via [Composer](https://getcomposer.org/):

\`\`\`bash
composer require jeishanul/image-converter
\`\`\`

### PHP Extensions Required

- \`ext-imagick\` (**recommended** for broad format support)
- \`ext-gd\` (fallback for basic formats: JPEG, PNG, GIF, BMP, WebP)

> ℹ️ For advanced formats like **HEIC**, **RAW**, **PDF**, ensure your system has dependencies such as **libheif**, **Ghostscript**, etc.

---

## 🖼️ Supported Formats

### ✅ Input Formats

\`\`\`
BMP, JPEG, JPG, PNG, GIF, TIFF, TIF, WebP, HEIC, HEIF, PSD, ICO,
SVG, AI, EPS, PDF, RAW, CR2, NEF, ARW, DNG, RW2, ORF, PEF, SRF,
SR2, EXR, DDS, APNG, JFIF, AVIF
\`\`\`

### ✅ Output Formats

Same as input formats (depending on support by Imagick or GD).

---

## 🔧 Usage

### Simple Conversion

Automatically converts and saves the image with the same filename and new extension:

\`\`\`php
use Jeishanul\ImageConverter\ImageConverter;

try {
    ImageConverter::convertSimple('input/sample.png', 'jpg');
    echo "Image converted successfully to sample.jpg!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
\`\`\`

---

### Advanced Conversion

Control output path and quality (0–100):

\`\`\`php
use Jeishanul\ImageConverter\ImageConverter;

try {
    $converter = new ImageConverter(
        'input/sample.png',      // Input file
        'output/sample.jpg',     // Output file
        'jpg',                   // Target format
        85                       // Quality
    );

    $converter->convert();
    echo "Image converted successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
\`\`\`

---

## ⚙️ Requirements

- **PHP** 7.4 or 8.0+
- **Imagick extension** (preferred)
- **GD extension** (fallback for basic formats)

---

## 📌 Notes

- **Imagick** is the primary engine, supporting most formats including **vector** and **RAW** types.
- **GD** handles common raster formats (JPEG, PNG, etc.) if Imagick is not available.
- **Vector formats** (SVG, AI, EPS, PDF) are rasterized to flat images (white background).
- **Quality settings** (0–100) apply to lossy formats (e.g., JPEG, WebP).
  - For lossless formats (e.g., PNG), quality controls compression level.
- Ensure system libraries like **Ghostscript**, **libheif**, and **libraw** are installed for full compatibility.

---

## 📄 License

[MIT License](https://opensource.org/licenses/MIT)

---

## 🧑‍💻 Author

Developed by **Jeishanul**