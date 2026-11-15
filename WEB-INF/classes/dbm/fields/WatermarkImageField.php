<?php 

require_once ("dbm/fields/NewImageField.php");

class WatermarkImageField extends NewImageField {

    var $dbColumnName = "value_str";
    var $tmpFilePath = "";
    var $tmpFileName = "";
    var $deleteOnStore = false;
    var $oldValue = "";
    private $watermark_positions = array(
        'cb', 'cc', 'lb', 'lt', 'rb', 'rt'
    );

    function __construct() {
        $pt = PhpThumb::getInstance();
        $pt->registerPlugin('GdWatermarkLib', 'gd');
    }

    protected function _img_set_additional($thumb, $fieldDef) {

        $use_watermark = true;
        if (isset($_POST['use_watermark_' . $fieldDef->shortname]) && $_POST['use_watermark_' . $fieldDef->shortname] == 0) {
            $use_watermark = false;
        }
        if ($use_watermark) {
            foreach ($this->watermark_positions as $k => $v) {
                if (strlen($fieldDef->fieldParameters[$v]) && file_exists($_SERVER['DOCUMENT_ROOT'] . $fieldDef->fieldParameters[$v])) {
                    $mark_image = $_SERVER['DOCUMENT_ROOT'] . $fieldDef->fieldParameters[$v];
                    $position = $v;
                    $padding = (int) $fieldDef->fieldParameters[$v . '_padding'];
                    $thumb->createWatermark($mark_image, $position, $padding);
                }
            }
        }
        return $thumb;
    }

}

?>