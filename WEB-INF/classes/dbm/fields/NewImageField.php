<?php 

require_once ("dbm/fields/StringField.php");
require_once ("imageclass.php");

class NewImageField extends Field {

    var $dbColumnName = "value_str";
    var $tmpFilePath = "";
    var $tmpFileName = "";
    var $deleteOnStore = false;
    var $oldValue = "";

    function __construct() {
        
    }

    function crop($exp0, $exp1, $exp, $CROP_DIR, $width, $height, $rs, $fieldDef) {

        $tmp_dir = "/tmp/" . $exp0 . "/finish/";
        // (int)/(float): empty parameters are 0 in PHP 5 arithmetic but a TypeError in PHP 8
        $width = (int)$width;
        $height = (int)$height;
        $img_thumb_exp = explode(":", (string)$exp);
        foreach ($img_thumb_exp as $k => $v) {
            $v = (float)$v;
            if ($v < 0)
                $v = 0;
            $img_thumb_exp[$k] = abs(ceil($v));
        }
        $img_thumb_exp += array(0, 0, 0, 0); // a short crop string ("" or "a:b") left [2]/[3] undefined
        $cropHeight = $img_thumb_exp[3] - $img_thumb_exp[2];
        $cropWidth = $img_thumb_exp[1] - $img_thumb_exp[0];

        FileWorks::mkdir_recursive($CROP_DIR . $tmp_dir);

        list($i_width, $i_height, $i_type, $i_attr) = getimagesize($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $exp0 . "/end/" . $exp1);
        if (($height >= $i_height && $width >= $i_width) || ($width >= $i_width && $rs == "bywidth") || ($height >= $i_height && $rs == "byheight") || ($rs == "bygreaterside" && ($i_width >= $i_height && $i_width <= $width || $i_height >= $i_width && $i_height <= $height)) || ($rs == "bysmallerside" && ($i_width <= $i_height && $i_width <= $width || $i_height <= $i_width && $i_height <= $height))
        ) {
            if ($fieldDef->fieldTypeId == 24) {
                $size = getimagesize($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $exp0 . "/end/" . $exp1);
                $thumb = PhpThumbFactory::create($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $exp0 . "/end/" . $exp1);
                $thumb->resize($size[0]);
                $thumb = $this->_img_set_additional($thumb, $fieldDef);
                $thumb->save($CROP_DIR . $tmp_dir . $exp1);
            } else {
                copy($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $exp0 . "/end/" . $exp1, $CROP_DIR . $tmp_dir . $exp1);
            }
        } else {

            $thumb = PhpThumbFactory::create($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $exp0 . "/end/" . $exp1);
            $thumb->crop($img_thumb_exp[0], $img_thumb_exp[2], $cropWidth, $cropHeight);

            if ($rs == "bygreaterside") {
                if ($cropHeight > $cropWidth) {
                    $thumb->resize(0, $height);
                    $width = $cropWidth * $height / $cropHeight;
                } else {
                    $thumb->resize($width);
                    $height = $cropHeight * $width / $cropWidth;
                }
            } elseif ($rs == "bysmallerside") {
                if ($cropWidth > $cropHeight) {
                    $thumb->resize(0, $height);
                    $width = $cropWidth * $height / $cropHeight;
                } else {
                    $thumb->resize($width);
                    $height = $cropHeight * $width / $cropWidth;
                }
            } elseif ($rs == "byheight") {
                $thumb->resize(0, $height);
                $width = $cropWidth * $height / $cropHeight;
            } elseif ($rs == "bywidth") {
                $thumb->resize($width);
                $height = $cropHeight * $width / $cropWidth;
            } elseif ($rs == "crop") {
                $new_ratio = $width / $height;
                $thumb->setCurrentDimensions(array('width' => $cropWidth, 'height' => $cropWidth / $new_ratio));
                $thumb->resize($width, $height);
            }

            $thumb = $this->_img_set_additional($thumb, $fieldDef);

            $thumb->save($CROP_DIR . $tmp_dir . $exp1);
        }

        $obj = new PhpObject();
        $obj->img = $thumb;
        $obj->width = $width;
        $obj->height = $height;
        $obj->tmp_dir = "/tmp/" . $exp0 . "/finish/";
        return $obj;
    }

    protected function _img_set_additional($thumb, $fieldDef) {
        return $thumb;
    }

    function setValue($o) {
        global $AdminTrnsl;
        $del = NULL;
        if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
            $request = $o;

            $fieldDef = $this->getFieldDef();
            $rValue = $o->getParameter($fieldDef->shortname);
            $del = (int) $request->getParameter($fieldDef->shortname . "_del");
            $file = $request->getParameter($fieldDef->shortname . "_file");
            $this->tmpFileName = $file->name;
            $this->tmpFilePath = $file->tmp_name;
        } elseif (is_string($o)) {
            $rValue = $o;
        }
        if ($del) {
            $this->remove();
            $this->value = "";
        } else {

            $resizeStrategy = $fieldDef->fieldParameters["resize_strategy"];
            $thumb_resizeStrategy = $fieldDef->fieldParameters["resize_strategy"];
            $exp = explode("#", $rValue);
            if (count($exp) > 2) {
                $this->remove();

                $height = $fieldDef->fieldParameters["thumbnail_height"];
                $width = $fieldDef->fieldParameters["thumbnail_width"];
                $rs = $fieldDef->fieldParameters["thumb_resize_strategy"];
                $img_obj = $this->crop($exp[0], $exp[1], $exp[2], FIELD_IMGLIB_THUMBNAILS_DIR, $width, $height, $rs, $fieldDef);

                $thumb_adds = explode("#", $fieldDef->fieldParameters["thumb_adds"]);
                if (strlen($exp[4]) && isset($thumb_adds[0]) && strlen($thumb_adds[0])) {
                    $ps = explode(":", $thumb_adds[0]);
                    if (count($ps) == 3) {
                        $width = $ps[0];
                        $height = $ps[1];
                        $rs = $ps[2];
                        $img_obj = $this->crop($exp[0], $exp[1], $exp[4], FIELD_IMGLIB_THUMBNAILS2_DIR, $width, $height, $rs, $fieldDef);
                    }
                }
                if (strlen($exp[5]) && isset($thumb_adds[1]) && strlen($thumb_adds[1])) {
                    $ps = explode(":", $thumb_adds[1]);
                    if (count($ps) == 3) {
                        $width = $ps[0];
                        $height = $ps[1];
                        $rs = $ps[2];
                        $img_obj = $this->crop($exp[0], $exp[1], $exp[5], FIELD_IMGLIB_THUMBNAILS3_DIR, $width, $height, $rs, $fieldDef);
                    }
                }


                $height = $fieldDef->fieldParameters["height"];
                $width = $fieldDef->fieldParameters["width"];
                $rs = $fieldDef->fieldParameters["resize_strategy"];
                $img_obj = $this->crop($exp[0], $exp[1], $exp[3], FIELD_IMGLIB_DIR, $width, $height, $rs, $fieldDef);

                $this->value = $img_obj->tmp_dir . $exp[1];
            } else {
                $this->value = $rValue;
            }
        }
        return;
    }

    function create($node_id=0) {
        $this->update();
    }

    function update() {
        if (strpos($this->value, "/tmp/") !== false) {
            $node = Node::findById($this->nodeId);
            $fieldDef = $this->getFieldDef();
            if ($fieldDef->fieldParameters["parent_save"] == 'false') {
                $imgPath = QNode::getImgPath($node);
                $prefix = '';
            } else {
                $imgPath = QNode::getImgPath($node->getParent());
                $prefix = $node->id . '_';
            }
            directoryClass::createDirIfNotExists($imgPath . "/", array(FIELD_IMGLIB_DIR, FIELD_IMGLIB_THUMBNAILS_DIR));
            if ($fieldDef->fieldParameters["save_original"] == 1) {
                directoryClass::createDirIfNotExists($imgPath . "/", array(FIELD_IMGLIB_ORIGINAL_DIR));
            }
            $path_parts = pathinfo(FIELD_IMGLIB_DIR . $this->value);
            @copy(FIELD_IMGLIB_DIR . $this->value, FIELD_IMGLIB_DIR . $imgPath . "/" . $prefix . $path_parts["basename"]);
            @copy(FIELD_IMGLIB_THUMBNAILS_DIR . $this->value, FIELD_IMGLIB_THUMBNAILS_DIR . $imgPath . "/" . $prefix . $path_parts["basename"]);

            if (file_exists(FIELD_IMGLIB_THUMBNAILS2_DIR . $this->value)) {
                directoryClass::createDirIfNotExists($imgPath . "/", array(FIELD_IMGLIB_THUMBNAILS2_DIR));
                @copy(FIELD_IMGLIB_THUMBNAILS2_DIR . $this->value, FIELD_IMGLIB_THUMBNAILS2_DIR . $imgPath . "/" . $prefix . $path_parts["basename"]);
            }

            if (file_exists(FIELD_IMGLIB_THUMBNAILS3_DIR . $this->value)) {
                directoryClass::createDirIfNotExists($imgPath . "/", array(FIELD_IMGLIB_THUMBNAILS3_DIR));
                @copy(FIELD_IMGLIB_THUMBNAILS3_DIR . $this->value, FIELD_IMGLIB_THUMBNAILS3_DIR . $imgPath . "/" . $prefix . $path_parts["basename"]);
            }
            if ($fieldDef->fieldParameters["save_original"] == 1) {
				//edump(str_replace("/finish", "/start", FIELD_IMGLIB_DIR . $this->value));
                @copy(str_replace(array("/finish",'/i/tmp/'), array("/start",'/tmp/'), FIELD_IMGLIB_DIR . $this->value), FIELD_IMGLIB_ORIGINAL_DIR . $imgPath . "/" . $prefix . $path_parts["basename"]);
            }
            $dirs = explode("/tmp/", $path_parts["dirname"]);
            $dirs1 = explode("/", $dirs[1]);
            directoryClass::rmdir($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $dirs1[0]);
            directoryClass::rmdir( FIELD_IMGLIB_DIR . "/tmp/" . $dirs1[0]);
            directoryClass::rmdir( FIELD_IMGLIB_THUMBNAILS_DIR . "/tmp/" . $dirs1[0]);
            directoryClass::rmdir( FIELD_IMGLIB_THUMBNAILS2_DIR . "/tmp/" . $dirs1[0]);
            directoryClass::rmdir( FIELD_IMGLIB_THUMBNAILS3_DIR . "/tmp/" . $dirs1[0]);
            directoryClass::rmdir( FIELD_IMGLIB_ORIGINAL_DIR . "/tmp/" . $dirs1[0]);
            //edump($_SERVER['DOCUMENT_ROOT'] . FIELD_IMGLIB_DIR . "/tmp/" . $dirs1[0]);
            $this->value = preg_replace('/\/+/', '/', $imgPath . "/" . $prefix . $path_parts["basename"]);
        }
    }

    function remove($val = "") {
        if (!mb_strlen($val))
            $val = $this->value;
        if (file_exists(FIELD_IMGLIB_DIR . $val))
            @unlink(FIELD_IMGLIB_DIR . $val);
        if (file_exists(FIELD_IMGLIB_THUMBNAILS_DIR . $val))
            @unlink(FIELD_IMGLIB_THUMBNAILS_DIR . $val);
        if (file_exists(FIELD_IMGLIB_THUMBNAILS2_DIR . $val))
            @unlink(FIELD_IMGLIB_THUMBNAILS2_DIR . $val);
        if (file_exists(FIELD_IMGLIB_THUMBNAILS3_DIR . $val))
            @unlink(FIELD_IMGLIB_THUMBNAILS3_DIR . $val);
        if (file_exists(FIELD_IMGLIB_ORIGINAL_DIR . $val))
            @unlink(FIELD_IMGLIB_ORIGINAL_DIR . $val);
        if (substr_count($val, "/tmp/") > 0) {
            $exp = explode("/tmp/", $val);
            $exp1 = explode("/", $exp[1]);
            directoryClass::rmdir($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $exp1[0]);
        }
        return true;
    }

    function getParamersForIncludeControlJSP() {
        $params = array();

        $fieldDef = $this->getFieldDef();
        $fieldType = $fieldDef->getFieldType();
        $classParams = $fieldType->getParametersNames();

        foreach ($classParams as $fieldParameterShortname) {
            $params[$fieldParameterShortname] = $fieldDef->getParameterValue($fieldParameterShortname);
        }

        $size_tag = "";
        $url = $this->value;
        if ($url != NULL && mb_strlen($url)) {
            $img = new PhpObject();
            if (file_exists(FIELD_IMGLIB_DIR . $url)) {
                list($img->getWidth, $img->getHeight) = getimagesize(FIELD_IMGLIB_DIR . $url);
                list($img->getThumbnailWidth, $img->getThumbnailHeight) = getimagesize(FIELD_IMGLIB_THUMBNAILS_DIR . $url);
                $img->getFileSize = filesize(FIELD_IMGLIB_DIR . $url);
                $img->getUrl = $url;

                $params["size_tag"] = " width=" . $img->getWidth . " height=" . $img->getHeight;
                $params["width"] = $img->getWidth;
                $params["height"] = $img->getHeight;
                $params["url"] = "/s/i" . $img->getUrl;
                $params["relurl"] = $img->getUrl;

                $params["thumburl"] = "/s/ithumb" . $img->getUrl;
                $params["thumbwidth"] = $img->getThumbnailWidth;
                $params["thumbheight"] = $img->getThumbnailHeight;

                $fsize = $img->getFileSize;
                $params["size"] = $fsize;
                $params["sizeKB"] = round($fsize / 1024);
                $params["sizeMB"] = round($fsize / 1024 / 1024);
            } else {
                $params["size_tag"] = "";
                $params["width"] = -1;
                $params["height"] = -1;
                $params["url"] = "";
                $params["relurl"] = "";

                $params["thumburl"] = "";
                $params["thumbwidth"] = -1;
                $params["thumbheight"] = -1;

                $fsize = -1;
                $params["size"] = -1;
                $params["sizeKB"] = -1;
                $params["sizeMB"] = -1;
            }
        }

        return $params;
    }

    function getValueForTemplate() {
        return $this->getParamersForIncludeControlJSP();
    }

    function getValue() {
        $url = $this->value;
        if ($url != null && mb_strlen($url) != 0) {
            $img = ImgLibImage::findByUrl($url);
            if ($img != null) {
                return "/imglib" . $img->getUrl();
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    function getHtmlVisualValue() {
        return ($this->value) ? '<img src="/s/ithumb' . $this->value . '" class="admin_preview" />' : '&nbsp;';
    }

    function getJSPControlName() {
        $fieldDef = $this->getFieldDef();
        $ctype = $fieldDef->getParameterValue("control_type");
        if ($ctype == null || $ctype == "") {
            $ctype = "normal";
        }

        $class = strtolower(get_class($this));
        $cname = $class . "_" . $ctype;
        return $cname;
    }

    static function getDefaultValue($fd) {
        $value = "";
        return $value;
    }

}

?>