<?php

class Image {
    /**
     * @var string The new filename for the image.
     */
    public $newFilename;

    /**
     * @var int $jpegQuality The quality of the saved jpeg image, from 0 (lowest) to 100 (highest). Default is 80.
     */
    public $jpegQuality = 80;

    /**
     *
     * Resize Image Difinitions
     *
     */

    /**
     * @var int The rotation angle for image resizing.
     */
    public $resizeRotation = 0;

    /**
     * @var string The background color in hexadecimal format (#RRGGBB) for image resizing.
     */
    public $resizeBgColor = '#ffffff';

    /**
     * @var int The maximum width for image resizing.
     */
    public $resizeMaxWidth = 0;

    /**
     * @var int The maximum height for image resizing.
     */
    public $resizeMaxHeight = 0;

    /**
     *
     * Text to Image Difinitions
     *
     */

    /**
     * @var int Font size for the text
     */
    public $fontSize = 80;

    /**
     * @var int Rotation angle of the font
     */
    public $fontRotation = 0;

    /**
     * @var int X-coordinate of the starting position for the text
     */
    public $fontLocationX = 80;

    /**
     * @var int Y-coordinate of the starting position for the text
     */
    public $fontLocationY = 80;

    /**
     * @var string Color of the font in hexadecimal format (e.g., "#FF0000" for red)
     */
    public $fontColor = '#ffffff';

    /**
     * @var string File path to the TrueType font file to be used
     */
    public $fontPath = '';

    /**
     * @var string Text for the first line
     */
    public $textLine1 = '';

    /**
     * @var string Text for the second line
     */
    public $textLine2 = '';

    /**
     * @var string Text for the third line
     */
    public $textLine3 = '';

    /**
     * @var int Vertical spacing between lines of text
     */
    public $textLineSpacing = 90;

    /**
     *
     * Apply Frame to Image Difinitions
     *
     */

    /**
     * @var string File path to the frame image (PNG)
     */
    public $framePath = '';

    /**
     * @var bool Whether to extend the frame to fit the source image
     */
    public $frameExtend = false;

    /**
     * @var int The percentage of extension to the left side of the frame
     */
    public $frameExtendLeft = 0;

    /**
     * @var int The percentage of extension to the right side of the frame
     */
    public $frameExtendRight = 0;

    /**
     * @var int The percentage of extension to the bottom of the frame
     */
    public $frameExtendBottom = 0;

    /**
     * @var int The percentage of extension to the top of the frame
     */
    public $frameExtendTop = 0;

    /**
     *
     * QR Difinitions
     *
     */

    /**
     * @var bool $qrAvailable QR library available or not.
     */
    public $qrAvailable = false;

    /**
     * @var bool $qrRotate Whether or not to rotate the QR code.
     */
    public $qrRotate = false;

    /**
     * @var string $qrPosition The position to place the QR code on the image.
     */
    public $qrPosition = 'bottom-right';

    /**
     * @var int $qrOffset The offset in pixels from the specified QR code position.
     */
    public $qrOffset = 0;

    /**
     * @var int $qrSize The size of the QR code, numeric value within the range of 2 to 10 and even.
     */
    public $qrSize = 4;

    /**
     * @var int $qrMargin The margin size around the QR code, must be in range between 0 and 10.
     */
    public $qrMargin = 4;

    /**
     * @var string $qrColor The color to apply to the QR code pixels.
     */
    public $qrColor = '#ffffff';

    /**
     * @var int The error correction level for the QR code (QR_ECLEVEL_L, QR_ECLEVEL_M, QR_ECLEVEL_Q, or QR_ECLEVEL_H)
     */
    public $qrEcLevel = '';

    /**
     * @var string $qrUrl The URL to generate a QR code for.
     */
    public $qrUrl = '';

    /**
     *
     * Polaroid Effect Difinitions
     *
     */

    /**
     * @var string The background color for the polaroid effect in hexadecimal format (e.g., '#c8c8c8').
     */
    public $polaroidBgColor = '#c8c8c8';

    /**
     * @var int The rotation angle for the polaroid effect in degrees.
     */
    public $polaroidRotation = 0;

    /**
     * QR constructor.
     * Includes the QR code library.
     */
    public function __construct() {
        if (file_exists('../vendor/phpqrcode/lib/full/qrlib.php')) {
            include '../vendor/phpqrcode/lib/full/qrlib.php';
            $this->qrEcLevel = QR_ECLEVEL_M;
            $this->qrAvailable = true;
        }
    }

    /**
     * Creates a new filename for the image.
     *
     * @param string $naming The naming convention to use for the filename. Options are "random" or "dateformatted".
     * @param string $ext The file extension to use for the filename. Default is ".jpg".
     * @return string The new filename.
     */
    public static function create_new_filename($naming = 'random', $ext = '.jpg') {
        if ($naming === 'dateformatted') {
            $name = date('Ymd_His') . $ext;
        } else {
            $name = md5(microtime()) . $ext;
        }
        return $name;
    }

    /**
     * Sets the new filename for the image using the specified naming convention.
     *
     * @param string $naming The naming convention to use for the filename. Options are "random" or "dateformatted".
     */
    public function set_new_filename($naming) {
        $this->newFilename = $this->create_new_filename($naming);
    }

    /**
     * Returns the new filename for the image.
     *
     * @return string The new filename.
     */
    public function get_new_filename() {
        return $this->newFilename;
    }

    /**
     * Sets the new filename for the image using the specified naming convention and returns the new filename.
     *
     * @param string $naming The naming convention to use for the filename. Options are "random" or "dateformatted".
     * @return string The new filename.
     */
    public function set_and_get_new_filename($naming) {
        $this->set_new_filename($naming);
        return $this->newFilename;
    }

    /**
     * Creates a GD image resource from an image file.
     *
     * @param string $image The file path or URL of the image to create a resource from.
     * @return resource|false Returns the GD image resource if successful, or false if an error occurs.
     */
    public static function createFromImage($image) {
        try {
            $resource = imagecreatefromstring(file_get_contents($image));
            if (!$resource) {
                throw new Exception('Can\'t create GD resource.');
            }
            return $resource;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validates a GD image resource.
     *
     * @param resource|null $resource The GD image resource to validate.
     * @return bool Returns true if the resource is a valid GD image resource, or false otherwise.
     */
    private function validateGdResource($resource) {
        try {
            if (!isset($resource) || !is_resource($resource) || get_resource_type($resource) !== 'gd') {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Saves a GD image resource to disk.
     *
     * @param resource $sourceResource The GD image resource to save.
     * @param string $destination The file path and name where the image will be saved.
     *
     * @return bool Returns true on success, or false on failure.
     */
    public function saveJpeg($sourceResource, $destination) {
        try {
            // Check if the $sourceResource and $destination are defined
            if (!isset($sourceResource) || !isset($destination)) {
                throw new Exception('Missing parameters.');
            }

            // Save the image to disk
            if (!imagejpeg($sourceResource, $destination, $this->jpegQuality)) {
                throw new Exception('Error saving image.');
            }

            return true;
        } catch (Exception $e) {
            // If there is an exception, return false
            return false;
        }
    }

    /**
     * Rotate and resize an image.
     *
     * @param resource $image The image resource to be rotated and resized.
     * @return resource The rotated and resized image resource, or the original image if an error occurs.
     */
    public function rotateResizeImage($image) {
        try {
            if (!$image) {
                throw new InvalidArgumentException('Invalid image resource');
            }

            $rotation = $this->resizeRotation;

            // simple rotate if possible and ignore changed dimensions (doesn't need to care about background color)
            $simple_rotate = [-180, -90, 0, 180, 90, 360];
            if (in_array($rotation, $simple_rotate)) {
                $new = imagerotate($image, $rotation, 0);
                if (!$new) {
                    throw new Exception('Cannot rotate image.');
                }
            } else {
                if (strlen($bg_color) === 7) {
                    $bg_color .= '00';
                }
                list($bg_r, $bg_g, $bg_b, $bg_a) = sscanf($this->resizeBgColor, '#%02x%02x%02x%02x');

                // get old dimensions
                $old_width = imagesx($image);
                $old_height = imagesy($image);

                // create new image with old dimensions
                $new = imagecreatetruecolor($old_width, $old_height);
                if (!$new) {
                    throw new Exception('Cannot create new image.');
                }

                // color background as defined
                $background = imagecolorallocatealpha($new, $bg_r, $bg_g, $bg_b, $bg_a);
                if (!imagefill($new, 0, 0, $background)) {
                    throw new Exception('Cannot fill image.');
                }

                // rotate the image
                $background = imagecolorallocatealpha($image, $bg_r, $bg_g, $bg_b, $bg_a);
                $image = imagerotate($image, $rotation, $background);
                if (!$image) {
                    throw new Exception('Cannot rotate image.');
                }

                // make sure width and/or height fits into old dimensions
                $this->resizeMaxWidth = $old_width;
                $this->resizeMaxHeight = $old_height;
                $image = self::resizeImage($image);
                if (!$image) {
                    throw new Exception('Cannot resize image.');
                }

                // get new dimensions after rotate and resize
                $new_width = imagesx($image);
                $new_height = imagesy($image);

                // center rotated image
                $x = ($old_width - $new_width) / 2;
                $y = ($old_height - $new_height) / 2;

                // copy rotated image to new image with old dimensions
                if (imagecopy($new, $image, $x, $y, 0, 0, $new_width, $new_height)) {
                    throw new Exception('Cannot copy rotated image to new image.');
                }
            }
        } catch (Exception $e) {
            // Try to clear cache
            if (is_resource($new)) {
                imagedestroy($new);
            }
            // Return unmodified resource
            return $image;
        }

        return $new;
    }

    /**
     * Resize an image based on the maximum dimensions.
     *
     * @param resource $image The image resource to be resized.
     * @return resource The resized image resource, or the original image if an error occurs.
     */
    public function resizeImage($image) {
        try {
            if (!$image) {
                throw new InvalidArgumentException('Invalid image resource.');
            }

            $old_width = imagesx($image);
            $old_height = imagesy($image);
            $max_width = $this->resizeMaxWidth;
            $max_height = $this->resizeMaxHeight;

            if ($old_width <= 0 || $old_height <= 0 || $max_width <= 0 || $max_height <= 0) {
                throw new InvalidArgumentException('Invalid image dimensions or maximum dimensions.');
            }

            $scale = min($max_width / $old_width, $max_height / $old_height);

            $new_width = ceil($scale * $old_width);
            $new_height = ceil($scale * $old_height);

            $new_image = imagescale($image, $new_width, $new_height, IMG_TRIANGLE);
            if (!$new_image) {
                throw new Exception('Cannot resize image.');
            }
        } catch (Exception $e) {
            // Return unmodified resource
            return $image;
        }

        return $new_image;
    }

    /**
     * Resize a PNG image based on the maximum dimensions.
     *
     * @param resource $image The image resource to be resized.
     * @return resource The resized PNG image resource, or the original image if an error occurs.
     */
    public function resizePngImage($image) {
        try {
            if (!$image) {
                throw new InvalidArgumentException('Invalid image resource.');
            }

            $old_width = imagesx($image);
            $old_height = imagesy($image);
            $max_width = $this->resizeMaxWidth;
            $max_height = $this->resizeMaxHeight;

            if ($old_width <= 0 || $old_height <= 0 || $max_width <= 0 || $max_height <= 0) {
                throw new InvalidArgumentException('Invalid image dimensions or maximum dimensions.');
            }

            $scale = min($max_width / $old_width, $max_height / $old_height);
            $new_width = ceil($scale * $old_width);
            $new_height = ceil($scale * $old_height);

            $new = imagecreatetruecolor($new_width, $new_height);
            if (!$new) {
                throw new Exception('Cannot create new image.');
            }

            imagealphablending($new, false);
            imagesavealpha($new, true);

            if (!imagecopyresized($new, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height)) {
                throw new Exception('Cannot resize image.');
            }
        } catch (Exception $e) {
            // Try to clear cache
            if (is_resource($new)) {
                imagedestroy($new);
            }
            // Return unmodified resource
            return $image;
        }

        return $new;
    }

    /**
     * Resize and crop an image by center.
     *
     * @param resource $source_file The source image resource to be resized and cropped.
     * @return resource The resized and cropped image resource, or the original image if an error occurs.
     */
    public function resizeCropImage($source_file) {
        try {
            $old_width = intval(imagesx($source_file));
            $old_height = intval(imagesy($source_file));
            $max_width = $this->resizeMaxWidth;
            $max_height = $this->resizeMaxHeight;

            if ($old_width <= 0 || $old_height <= 0 || $max_width <= 0 || $max_height <= 0) {
                throw new InvalidArgumentException('Invalid image dimensions or maximum dimensions.');
            }

            $new_width = intval(($old_height * $max_width) / $max_height);
            $new_height = intval(($old_width * $max_height) / $max_width);

            settype($max_width, 'integer');
            settype($max_height, 'integer');

            $new = imagecreatetruecolor(intval($max_width), intval($max_height));
            if (!$new) {
                throw new Exception('Cannot create new image.');
            }

            // If the new width is greater than the actual width of the image, then the height is too large and the rest is cut off, or vice versa
            if ($new_width > $old_width) {
                // Cut point by height
                $h_point = intval(($old_height - $new_height) / 2);
                // Copy image
                if (!imagecopyresampled($new, $source_file, 0, 0, 0, $h_point, $max_width, $max_height, $old_width, $new_height)) {
                    throw new Exception('Cannot resize and crop image by height.');
                }
            } else {
                // Cut point by width
                $w_point = intval(($old_width - $new_width) / 2);
                if (!imagecopyresampled($new, $source_file, 0, 0, $w_point, 0, $max_width, $max_height, $new_width, $old_height)) {
                    throw new Exception('Cannot resize and crop image by width.');
                }
            }
        } catch (Exception $e) {
            // Try to clear cache
            if (is_resource($new)) {
                imagedestroy($new);
            }
            // Return unmodified resource
            return $source_file;
        }

        return $new;
    }

    /**
     * Apply the frame to the source image resource
     *
     * @param resource $sourceResource The source image resource to which the frame will be applied
     * @return resource The modified source image resource with the frame applied
     */
    public function applyFrame($sourceResource) {
        try {
            if ($this->frameExtend) {
                $new_width = intval(imagesx($sourceResource) / (1 - 0.01 * ($this->frameExtendLeft + $this->frameExtendRight)));
                $new_height = intval(imagesy($sourceResource) / (1 - 0.01 * ($this->frameExtendTop + $this->frameExtendBottom)));

                $img = imagecreatetruecolor($new_width, $new_height);
                if (!$img) {
                    throw new Exception('Cannot create new image.');
                }
                $white = imagecolorallocate($img, 255, 255, 255);

                // We fill in the new white image
                if (!imagefill($img, 0, 0, $white)) {
                    throw new Exception('Cannot fill image.');
                }

                $image_pos_x = intval(imagesx($img) * 0.01 * $this->frameExtendLeft);
                $image_pos_y = intval(imagesy($img) * 0.01 * $this->frameExtendTop);

                // We copy the image to which we want to apply the frame in our new image.
                if (!imagecopy($img, $sourceResource, $image_pos_x, $image_pos_y, 0, 0, imagesx($sourceResource), imagesy($sourceResource))) {
                    throw new Exception('Error copying image to new frame.');
                }
            } else {
                $img = $sourceResource;
            }

            $pic_width = imagesx($img);
            $pic_height = imagesy($img);

            $frame = self::createFromImage($this->framePath);
            $this->resizeMaxWidth = $pic_width;
            $this->resizeMaxHeight = $pic_height;
            $frame = self::resizePngImage($frame);
            if (!$frame) {
                throw new Exception('Cannot resize Frame.');
            }
            $frame_width = imagesx($frame);
            $frame_height = imagesy($frame);

            $dst_x = 0;
            $dst_y = 0;

            if ($pic_height == $frame_height) {
                $dst_x = intval(($pic_width - $frame_width) / 2);
            } else {
                $dst_y = intval(($pic_height - $frame_height) / 2);
            }

            if (!imagecopy($img, $frame, $dst_x, $dst_y, 0, 0, $frame_width, $frame_height)) {
                throw new Exception('Error applying frame to image.');
            }
        } catch (Exception $e) {
            // Clear cache
            if (is_resource($img)) {
                imagedestroy($img);
            }
            // Return unmodified resource
            return $sourceResource;
        }

        // Return resource with text applied
        return $img;
    }

    /**
     * Apply text to the source image resource
     *
     * @param resource $sourceResource The source image resource to which text will be applied
     * @return resource The modified source image resource with text applied
     */
    public function applyText($sourceResource) {
        try {
            $fontSize = $this->fontSize;
            $fontRotation = $this->fontRotation;
            $fontLocationX = $this->fontLocationX;
            $fontLocationY = $this->fontLocationY;
            $fontPath = $this->fontPath;
            $textLineSpacing = $this->textLineSpacing;
            // Convert hex color string to RGB values
            list($r, $g, $b) = sscanf($this->fontColor, '#%02x%02x%02x');

            // Allocate color and set font
            $color = imagecolorallocate($sourceResource, $r, $g, $b);

            // Add first line of text
            if (!empty($this->textLine1)) {
                if (imagettftext($sourceResource, $fontSize, $fontRotation, $fontLocationX, $fontLocationY, $color, $fontPath, $this->textLine1)) {
                    throw new Exception('Could not add first line of text to resource.');
                }
            }

            // Add second line of text
            if (!empty($this->textLine2)) {
                $line2Y = $fontRotation < 45 && $fontRotation > -45 ? $fontLocationY + $textLineSpacing : $fontLocationY;
                $line2X = $fontRotation < 45 && $fontRotation > -45 ? $fontLocationX : $fontLocationX + $textLineSpacing;
                if (imagettftext($sourceResource, $fontSize, $fontRotation, $line2X, $line2Y, $color, $fontPath, $this->textLine2)) {
                    throw new Exception('Could not add second line of text to resource.');
                }
            }

            // Add third line of text
            if (!empty($this->textLine3)) {
                $line3Y = $fontRotation < 45 && $fontRotation > -45 ? $fontLocationY + $textLineSpacing * 2 : $fontLocationY;
                $line3X = $fontRotation < 45 && $fontRotation > -45 ? $fontLocationX : $fontLocationX + $textLineSpacing * 2;
                if (imagettftext($sourceResource, $fontSize, $fontRotation, $line3X, $line3Y, $color, $fontPath, $this->textLine3)) {
                    throw new Exception('Could not add third line of text to resource.');
                }
            }
        } catch (Exception $e) {
            // Return unmodified resource
            return $sourceResource;
        }

        // Return resource with text applied
        return $sourceResource;
    }

    /**
     * Generates a QR code image using the provided URL and configuration settings.
     *
     * @return resource An image resource of the generated QR code.
     *
     * @throws Exception If no URL for QR code generation is defined or if there are issues with image rotation.
     */
    public function createQr() {
        if (!$this->qrAvailable) {
            throw new Exception('QR library not available.');
        }

        if (empty($this->qrUrl)) {
            throw new Exception('No URL for QR-Code generation defined.');
        }

        if (!is_numeric($this->qrSize)) {
            throw new Exception('QR-Size is not numeric.');
        }
        if ($this->qrSize % 2 != 0) {
            throw new Exception('QR-Size is not even.');
        }
        if ($this->qrSize < 2 || $this->qrSize > 10) {
            throw new Exception('QR-Size must be 2, 4, 6, 8 or 10.');
        }

        if (!is_numeric($this->qrMargin)) {
            throw new Exception('QR-Margin is not numeric.');
        }
        if ($this->qrMargin < 0 || $this->qrMargin > 10) {
            throw new Exception('QR-Size must be in range between 0 and 10.');
        }

        $qrCode = QRcode::text($this->qrUrl, false, $this->qrEcLevel);
        $qrCodeImage = QRimage::image($qrCode, $this->qrSize, $this->qrMargin);
        if (!$qrCodeImage) {
            throw new Exception('Failed to create image from QR code.');
        }

        if ($this->qrRotate) {
            if (!imagerotate($qrCodeImage, 90, 0)) {
                throw new Exception('Unable to rotate QR-Code-Image.');
            }
        }
        if ($this->qrColor != '#ffffff') {
            $qrwidth = imagesx($qrCodeImage);
            $qrheight = imagesy($qrCodeImage);
            list($r, $g, $b) = sscanf($this->qrColor, '#%02x%02x%02x');
            $selected = imagecolorallocate($qrCodeImage, $r, $g, $b);

            for ($xpos = 0; $xpos < $qrwidth; $xpos++) {
                for ($ypos = 0; $ypos < $qrheight; $ypos++) {
                    $currentcolor = imagecolorat($qrCodeImage, $xpos, $ypos);
                    $parts = imagecolorsforindex($qrCodeImage, $currentcolor);

                    if ($parts['red'] == 255 && $parts['green'] == 255 && $parts['blue'] == 255) {
                        imagesetpixel($qrCodeImage, $xpos, $ypos, $selected);
                    }
                }
            }
        }
        return $qrCodeImage;
    }

    /**
     * Generates a QR code and displays it as a PNG image.
     *
     * @throws Exception If an error occurs during the generation of the QR code.
     */
    public function showQR() {
        try {
            // Generate the QR code
            $qrCode = $this->createQr();

            // Display the QR code as a PNG image
            imagepng($qrCode);
        } catch (Exception $e) {
            // If an exception is caught, display the error message
            echo $e->getMessage();
        }
    }

    /**
     * Generates a QR code and saves it to a specified destination path.
     *
     * @param string $destination The path where the QR code should be saved.
     *
     * @return bool True if the QR code was successfully saved, false otherwise.
     */
    public function saveQr($destination) {
        try {
            if (empty($destination)) {
                throw new Exception('No destination path given.');
            }

            // Generate the QR code
            $qrCode = $this->createQr();

            // Save the QR code as a PNG image to the specified destination path
            if (!imagepng($qrCode, $destination)) {
                throw new Exception('Unable to save QR code to ' . $destination);
            }

            // Return true if the QR code was successfully saved
            return true;
        } catch (Exception $e) {
            // If an exception is caught, return false
            return false;
        }
    }

    /**
     * Applies a generated QR code image to an existing image resource.
     *
     * @param resource $qrCode The QR code image resource to apply.
     * @param resource $imageResource The existing image resource to apply the QR code to.
     *
     * @return resource The updated image resource with the applied QR code.
     *
     * @throws Exception If the QR offset is not a numeric value.
     */
    public function applyQr($qrCode, $imageResource) {
        try {
            if (!is_numeric($this->qrOffset)) {
                throw new Exception('QR-Offset is not numeric.');
            }
            $offset = $this->qrOffset;

            $width = imagesx($imageResource);
            $height = imagesy($imageResource);
            $qrWidth = imagesx($qrCode);
            $qrHeight = imagesy($qrCode);

            if ($width <= 0 || $height <= 0 || $qrWidth <= 0 || $qrHeight <= 0) {
                throw new InvalidArgumentException('Invalid image dimensions or maximum dimensions.');
            }
            switch ($this->qrPosition) {
                case 'topLeft':
                    $x = $offset;
                    $y = $offset;
                    break;
                case 'top':
                    $x = ($width - $qrWidth) / 2;
                    $y = $offset;
                    break;
                case 'topRight':
                    $x = $width - ($qrWidth + $offset);
                    $y = $offset;
                    break;
                case 'right':
                    $x = $width - $qrWidth - $offset;
                    $y = ($height - $qrHeight) / 2;
                    break;
                case 'bottomRight':
                    $x = $width - ($qrWidth + $offset);
                    $y = $height - ($qrHeight + $offset);
                    break;
                case 'bottom':
                    $x = ($width - $qrWidth) / 2;
                    $y = $height - $qrHeight - $offset;
                    break;
                case 'bottomLeft':
                    $x = $offset;
                    $y = $height - ($qrHeight + $offset);
                    break;
                case 'left':
                    $x = $offset;
                    $y = ($height - $qrHeight) / 2;
                    break;
                default:
                    $x = $width - ($qrWidth + $offset);
                    $y = $height - ($qrHeight + $offset);
                    break;
            }

            if (!imagecopy($imageResource, $qrCode, $x, $y, 0, 0, $qrWidth, $qrHeight)) {
                throw new Exception('Can not apply QR Code onto image.');
            }
            // Try to clear cache
            if (is_resource($qrCode)) {
                imagedestroy($qrCode);
            }
            return $imageResource;
        } catch (Exception $e) {
            if (is_resource($qrCode)) {
                imagedestroy($qrCode);
            }
            return $imageResource;
        }
    }

    /**
     * Applies a polaroid effect to the given image resource.
     *
     * @param resource $resource The source image resource to apply the effect to.
     * @return resource The rotated image resource with the polaroid effect, or the original source image if an exception occurs.
     */
    public function effectPolaroid($resource) {
        try {
            // We create a new image
            $img = imagecreatetruecolor(imagesx($resource) + 25, imagesy($resource) + 80);
            if (!$img) {
                throw new Exception('Cannot create new image.');
            }
            $white = imagecolorallocate($img, 255, 255, 255);

            // We fill in the new white image
            if (!imagefill($img, 0, 0, $white)) {
                throw new Exception('Cannot fill image.');
            }

            // We copy the image to which we want to apply the polariod effect in our new image.
            if (!imagecopy($img, $resource, 11, 11, 0, 0, imagesx($resource), imagesy($resource))) {
                imagedestroy($img);
                throw new Exception('Cannot copy image.');
            }

            // Clear cach
            imagedestroy($resource);

            // Border color
            $color = imagecolorallocate($img, 192, 192, 192);
            // We put a gray border to our image.
            if (!imagerectangle($img, 0, 0, imagesx($img) - 4, imagesy($img) - 4, $color)) {
                imagedestroy($img);
                throw new Exception('Cannot add border.');
            }

            // Shade Colors
            $gris1 = imagecolorallocate($img, 208, 208, 208);
            $gris2 = imagecolorallocate($img, 224, 224, 224);
            $gris3 = imagecolorallocate($img, 240, 240, 240);

            // We add a small shadow
            if (
                !imageline($img, 2, imagesy($img) - 3, imagesx($img) - 1, imagesy($img) - 3, $gris1) ||
                !imageline($img, 4, imagesy($img) - 2, imagesx($img) - 1, imagesy($img) - 2, $gris2) ||
                !imageline($img, 6, imagesy($img) - 1, imagesx($img) - 1, imagesy($img) - 1, $gris3) ||
                !imageline($img, imagesx($img) - 3, 2, imagesx($img) - 3, imagesy($img) - 4, $gris1) ||
                !imageline($img, imagesx($img) - 2, 4, imagesx($img) - 2, imagesy($img) - 4, $gris2) ||
                !imageline($img, imagesx($img) - 1, 6, imagesx($img) - 1, imagesy($img) - 4, $gris3)
            ) {
                imagedestroy($img);
                throw new Exception('Cannot add shadow.');
            }

            // Convert hex color string to RGB values
            list($rbcc, $gbcc, $bbcc) = sscanf($this->polaroidBgColor, '#%02x%02x%02x');

            // We rotate the image
            $background = imagecolorallocate($img, $rbcc, $gbcc, $bbcc);
            $rotatedImg = imagerotate($img, $this->polaroidRotation, $background);

            if (!$rotatedImg) {
                throw new Exception('Cannot rotate image.');
            }
        } catch (Exception $e) {
            // Clear cache
            imagedestroy($img);
            // Return unmodified resource
            return $resource;
        }
        // We destroy the image we have been working with
        imagedestroy($img);

        // We return the rotated image
        return $rotatedImg;
    }
}