<?php

require_once ("directory.php");

class imageClass
{

    var $sourceFile;
    var $targetFile;
    var $resizeToWidth;
    var $resizeToHeight;
    var $maintainAspectRatio;
    var $resizeIfSmaller;
    var $resizeIfGreater;
    var $jpegOutputQuality;
    var $chmodValue;
    var $preserveSourceFileTime;
    var $error;
	var $o_width=0;
	var $o_height=0;
    function __construct()
    {
        $this->chmodValue = 0755;
        $this->preserveSourceFileTime = true;
        $this->error = 0;
        $this->jpegOutputQuality = 75;
        $this->resizeIfGreater = true;
        $this->resizeIfSmaller = true;
        $this->maintainAspectRatio = true;
        $this->resizeToHeight = -1;
        $this->resizeToWidth = -1;
        $this->targetFile = "";
        $this->sourceFile = "";
        
    }
	function setJpegOutputQuality($q=90){
		$this->jpegOutputQuality=$q;
	}
	function setSourceFile($sourceFile)
	{
		$this->sourceFile=$sourceFile;
		list($this->o_width, $this->o_height) =getimagesize($this->sourceFile);
	
	}
	function setTargetFile($targetFile){
		$this->targetFile=$targetFile;
	}
    function create_image_from_source_file()
    {
        if (!is_file($this->sourceFile) || !file_exists($this->sourceFile)) {
            $this->error = 1;
            return false;
        } elseif (!is_readable($this->sourceFile)) {
            $this->error = 2;
            return false;
        } elseif ($this->targetFile == $this->sourceFile && !is_writable($this->sourceFile)) {
            $this->error = 3;
            return false;
        } elseif (!list($sourceImageWidth, $sourceImageHeight, $sourceImageType) = @getimagesize($this->sourceFile)) {
            $this->error = 4;
            return false;
        } else {
            switch ($sourceImageType) {
            
                // if gif
                case 1:
                
                  /*  $fp = fopen($this->sourceFile, "rb");
                    $result = fread($fp, 13);
                    $colorFlag = ord(substr($result,10,1)) >> 7;
                    $background = ord(substr($result,11));
                    if ($colorFlag) {
                        $tableSizeNeeded = ($background + 1) * 3;
                        $result = fread($fp, $tableSizeNeeded);
                        $this->transparentColorRed = ord(substr($result, $background * 3, 1));
                        $this->transparentColorGreen = ord(substr($result, $background * 3 + 1, 1));
                        $this->transparentColorBlue = ord(substr($result, $background * 3 + 2, 1));
                    }
                    fclose($fp);
                    */
                    $sourceImageIdentifier = @imagecreatefromgif($this->sourceFile);
                    break;
                    
                // if jpg
                case 2:
                
                    $sourceImageIdentifier = @imagecreatefromjpeg($this->sourceFile);
                    break;
                    
                // if png
                case 3:
                
                    $sourceImageIdentifier = @imagecreatefrompng($this->sourceFile);
                    break;
                    
                default:
                
                    $this->error = 4;
                    return false;
                    
            }
            
        }
        
        if ($this->preserveSourceFileTime) {
        
            $this->sourceFileTime = filemtime($this->sourceFile);
        
        }
        return array($sourceImageIdentifier, $sourceImageWidth, $sourceImageHeight, $sourceImageType);
        
    }

    function create_target_image_identifier($width, $height)
    {
    
        // creates a blank image
        $targetImageIdentifier = imagecreatetruecolor((int)$width <= 0 ? 1 : (int)$width, (int)$height <= 0 ? 1 : (int)$height);
        
        // if we have transparency in the image
        if (isset($this->transparentColorRed) && isset($this->transparentColorGreen) && isset($this->transparentColorBlue)) {
        
            $transparent = imagecolorallocate($targetImageIdentifier, $this->transparentColorRed, $this->transparentColorGreen, $this->transparentColorBlue);
            imagefilledrectangle($targetImageIdentifier, 0, 0, $width, $height, $transparent);
            imagecolortransparent($targetImageIdentifier, $transparent);
            
        }
        
        // return target image identifier
        return $targetImageIdentifier;
        
    }

    /**
     *  creates a new image from a given image identifier
     *
     *  @access private
     */
    function output_target_image($targetImageIdentifier)
    {
    
        // get target file extension
        $targetFileExtension = strtolower(substr($this->targetFile, strrpos($this->targetFile, ".") + 1));
        
        // image saving process goes according to required extension
        switch ($targetFileExtension) {
        
            // if gif
            case "gif":
            
                // if gd support for this file type is not available
                if (!function_exists("imagegif")) {
                
                    // save the error level and stop the execution of the script
                    $this->error = 6;
                    return false;
                    
                // if, for some reason, file could not be created
                } elseif (@!imagegif($targetImageIdentifier, $this->targetFile)) {
                
                    // save the error level and stop the execution of the script
                    $this->error = 3;
                    return false;
                    
                }
                
                break;
                
            // if jpg
            case "jpg":
            case "jpeg":
            
                // if gd support for this file type is not available
                if (!function_exists("imagejpeg")) {
                
                    // save the error level and stop the execution of the script
                    $this->error = 6;
                    return false;
                    
                // if, for some reason, file could not be created
                } elseif (@!imagejpeg($targetImageIdentifier, $this->targetFile, $this->jpegOutputQuality)) {
                
                    // save the error level and stop the execution of the script
                    $this->error = 3;
                    return false;
                    
                }
                
                break;
                
            case "png":
            
                // if gd support for this file type is not available
                if (!function_exists("imagepng")) {
                
                    // save the error level and stop the execution of the script
                    $this->error = 6;
                    return false;
                    
                // if, for some reason, file could not be created
                } elseif (@!imagepng($targetImageIdentifier, $this->targetFile)) {
                
                    // save the error level and stop the execution of the script
                    $this->error = 3;
                    return false;
                    
                }
                
                break;
                
            // if not a supported file extension
            default:
            
                // save the error level and stop the execution of the script
                $this->error = 5;
                return false;
                
        }
        
        // if file was created successfully
        // chmod the file
        chmod($this->targetFile, intval($this->chmodValue, 8));
        
        
        // if the date/time of the target file should be the same as the source file's
        if ($this->preserveSourceFileTime) {
        
            // touch the newly created file
            @touch($this->targetFile, $this->sourceFileTime);
            
        }
        
        // and return true
        return true;
        
    }
	function resizeToWidth($width){
		$this->resizeToWidth=($this->o_width>$width)?$width:$this->o_width;	
		$this->resizeToHeight=$this->o_height*$this->resizeToWidth/$this->o_width;	
		return $this->resize();
	}
	function resizeToHeight($height){
		$this->resizeToHeight=floor(($this->o_height>$height)?$height:$this->o_height);	
		$this->resizeToWidth=floor($this->o_width*$this->resizeToHeight/$this->o_height);	
		return $this->resize();
	}
	function resizeToWidthAndHeight($width,$height){
		$this->resizeToWidth=floor(($this->o_width>$width)?$width:$this->o_width);	
		$this->resizeToHeight=floor(($this->o_height>$height)?$height:$this->o_height);	
		return $this->resize();
	}
	function resizeToMaxSide($max_size){
		return floor(($this->o_width>$this->o_height)?$this->resizeToWidth($max_size):$this->resizeToHeight($max_size));
	}
    /**
     *  Resizes the image given as {@link sourceFile} and outputs the resulted image as {@link targetFile}
     *  while following user specified properties
     *
     *  @return boolean     TRUE on success, FALSE on error.
     *                      If FALSE is returned, check the {@link error} property to see what went wrong
     */
    function resize()
    {

        // tries to create an image from sourceFile
        $result = $this->create_image_from_source_file();
        
        // if operation was successful
        if (is_array($result)) {
        
            list($sourceImageIdentifier, $sourceImageWidth, $sourceImageHeight, $sourceImageType) = $result;
        
            // if aspect ratio needs to be maintained
            if ($this->maintainAspectRatio) {
            
                // calculates image's aspect ratio
                $aspectRatio =
                    $sourceImageWidth <= $sourceImageHeight ?
                        $sourceImageHeight / $sourceImageWidth :
                        $sourceImageWidth / $sourceImageHeight;
                $targetImageWidth = $sourceImageWidth;
                $targetImageHeight = $sourceImageHeight;
                  
				  
    
                // if width of image is greater than resizeToWidth property and resizeIfGreater property is TRUE
                // or width of image is smaller than resizeToWidth property and resizeIfSmaller property is TRUE
                if (
                    ($this->resizeToWidth >= 0 && $targetImageWidth > $this->resizeToWidth && $this->resizeIfGreater) ||
                    ($this->resizeToWidth >= 0 && $targetImageWidth < $this->resizeToWidth && $this->resizeIfSmaller)
                ) {
                
                    // set the width of target image
                    $targetImageWidth = $this->resizeToWidth;
                    
                    // set the height of target image so that the image will keep its aspect ratio
                    $targetImageHeight =
                        ceil($sourceImageWidth <= $sourceImageHeight ?
                            $targetImageWidth * $aspectRatio :
                            $targetImageWidth / $aspectRatio);
                            
                    // saves the got width in case the next section wants to change it
                    $lockedTargetImageWidth = $targetImageWidth;
                    
                }
  
                // if height of image is greater than resizeToHeight property and resizeIfGreater property is TRUE
                // or height of image is smaller than resizeToHeight property and resizeIfSmaller property is TRUE
                if (
                    ($this->resizeToHeight >= 0 && $targetImageHeight > $this->resizeToHeight && $this->resizeIfGreater) ||
                    ($this->resizeToHeight >= 0 && $targetImageHeight < $this->resizeToHeight && $this->resizeIfSmaller)
                ) {
                
                    // set the width of target image
                    $targetImageHeight = $this->resizeToHeight;
                    
                    // set the width of target image so that the image will keep its aspect ratio
                    $targetImageWidth =
                        ceil($sourceImageWidth <= $sourceImageHeight ?
                            $targetImageHeight / $aspectRatio :
                            $targetImageHeight * $aspectRatio);
                           
                    // if maximum width was already set but has changed now
                    if (
                        isset($lockedTargetImageWidth) &&
                        $targetImageWidth > $lockedTargetImageWidth
                    ) {
                    
                        // adjust the height so that the width remains as set before
                        while ($targetImageWidth > $lockedTargetImageWidth) {
                        
                            $targetImageHeight--;
                            $targetImageWidth =
                                $sourceImageWidth <= $sourceImageHeight ?
                                    $targetImageHeight / $aspectRatio :
                                    $targetImageHeight * $aspectRatio;
                                    
                        }
                        
                    }
                    
                }
                
            // if aspect ratio does not need to be maintained
            } else {
            
                $targetImageWidth = ($this->resizeToWidth >= 0 ? $this->resizeToWidth : $sourceImageWidth);
                $targetImageHeight = ($this->resizeToHeight >= 0 ? $this->resizeToHeight : $sourceImageHeight);
                
            }
			//*****************************************************GR***************************************************/////
			$targetImageHeight=$this->resizeToHeight;
			$targetImageWidth=$this->resizeToWidth;
            // prepares the target image
            $targetImageIdentifier = $this->create_target_image_identifier($targetImageWidth, $targetImageHeight);

            // resizes image
            // but first if source image is png take care of transparency
            // this is to maintain transparency of png24 files
            if ($sourceImageType == 3) {
            
                imagealphablending($targetImageIdentifier, false);
                imagecopyresampled($targetImageIdentifier, $sourceImageIdentifier, 0, 0, 0, 0, $targetImageWidth, $targetImageHeight, $sourceImageWidth, $sourceImageHeight);
                imagesavealpha($targetImageIdentifier, true);
                
            // if image is something other than png
            } else {
            
                imagecopyresampled($targetImageIdentifier, $sourceImageIdentifier, 0, 0, 0, 0, $targetImageWidth, $targetImageHeight, $sourceImageWidth, $sourceImageHeight);
                
            }
            
            // writes image
            return $this->output_target_image($targetImageIdentifier);
            
        // if new image resource could not be created
        } else {
        
            // return false
            // note that we do not set the error level as it has been already set
            // by the create_image_from_source_file() method earlier
            return false;
            
        }
    }
    
    /**
     *  Crops a portion of the source file and puts it in target file
     *
     *  @param  integer     $src_x  x coordinate to start cropping from
     *  @param  integer     $src_y  y coordinate to start cropping from
     *  @param  integer     $dst_x  x coordinate where to end the cropping
     *  @param  integer     $dst_y  y coordinate where to end the cropping
     *
     *  @since  1.0.4
     *
     *  @return boolean     TRUE on success, FALSE on error.
     *                      If FALSE is returned, check the {@link error} property to see what went wrong
     */
    function crop($src_x, $src_y, $dst_x, $dst_y)
    {
    
        // tries to create an image from sourceFile
        $result = $this->create_image_from_source_file();

        // if operation was successful
        if (is_array($result)) {

            list($sourceImageIdentifier, $sourceImageWidth, $sourceImageHeight) = $result;

            // prepares the target image
            $targetImageIdentifier = $this->create_target_image_identifier($dst_x - $src_x, $dst_y - $src_y);

            // crops the image
           imagecopyresampled($targetImageIdentifier, $sourceImageIdentifier, 0, 0, $src_x, $src_y, $dst_x - $src_x, $dst_y - $src_y, $dst_x - $src_x, $dst_y - $src_y);

            // writes image
            return $this->output_target_image($targetImageIdentifier);
            
        }

    }

    /**
     *  Flips horizontally the image given as {@link sourceFile} and outputs the resulted image as {@link targetFile}
     *
     *  @return boolean     TRUE on success, FALSE on error.
     *                      If FALSE is returned, check the {@link error} property to see what went wrong
     */
    function flip_horizontal()
    {
    
        // tries to create an image from sourceFile
        $result = $this->create_image_from_source_file();

        // if operation was successful
        if (is_array($result)) {

            list($sourceImageIdentifier, $sourceImageWidth, $sourceImageHeight) = $result;
        
            // prepares the target image
            $targetImageIdentifier = $this->create_target_image_identifier($sourceImageWidth, $sourceImageHeight);
            
            // flips image horizontally
            for ($x = 0; $x < $sourceImageWidth; $x++) {
               imagecopyresampled($targetImageIdentifier, $sourceImageIdentifier, $x, 0, $sourceImageWidth - $x - 1, 0, 1, $sourceImageHeight);
            }
            
            // writes image
            return $this->output_target_image($targetImageIdentifier);
            
        // if new image resource could not be created
        } else {
        
            // return false
            // note that we do not set the error level as it has been already set
            // by the create_image_from_source_file() method earlier
            return false;
            
        }
        
    }

    /**
     *  Flips vertically the image given as {@link sourceFile} and outputs the resulted image as {@link targetFile}
     *
     *  @return boolean     TRUE on success, FALSE on error.
     *                      If FALSE is returned, check the {@link error} property to see what went wrong
     */
    function flip_vertical()
    {
    
        // tries to create an image from sourceFile
        $result = $this->create_image_from_source_file();

        // if operation was successful
        if (is_array($result)) {

            list($sourceImageIdentifier, $sourceImageWidth, $sourceImageHeight) = $result;
        
            // prepares the target image
            $targetImageIdentifier = $this->create_target_image_identifier($sourceImageWidth, $sourceImageHeight);
            
            // flips image vertically
            for ($y = 0; $y < $sourceImageHeight; $y++) {
                imagecopyresampled($targetImageIdentifier, $sourceImageIdentifier, 0, $y, 0, $sourceImageHeight - $y - 1, $sourceImageWidth, 1);
            }
            
            // writes image
            return $this->output_target_image($targetImageIdentifier);
            
        // if new image resource could not be created
        } else {
        
            // return false
            // note that we do not set the error level as it has been already set
            // by the create_image_from_source_file() method earlier
            return false;
            
        }
        
    }

    /**
     *  Rotates the image given as {@link sourceFile} and outputs the resulted image as {@link targetFile}
     *
     *  this method implements PHP's imagerotate method which is buggy.
     *  an improved version of this method should be available soon
     *
     *  @param  double  $angle      angle to rotate the image by
     *  @param  mixed   $bgColor    the color of the uncovered zone after the rotation
     *
     *  @return boolean     TRUE on success, FALSE on error.
     *                      If FALSE is returned, check the {@link error} property to see what went wrong
     */
    function rotate($angle, $bgColor)
    {
    
        // tries to create an image from sourceFile
        $result = $this->create_image_from_source_file();

        // if operation was successful
        if (is_array($result)) {

            list($sourceImageIdentifier, $sourceImageWidth, $sourceImageHeight, $sourceImageType) = $result;
        
            // rotates image
            $targetImageIdentifier = imagerotate($sourceImageIdentifier, $angle, $bgColor);
            
            // writes image
            return $this->output_target_image($targetImageIdentifier);
            
        // if new image resource could not be created
        } else {
        
            // return false
            // note that we do not set the error level as it has been already set
            // by the create_image_from_source_file() method earlier
            return false;
            
        }
        
    }
    
}
?>