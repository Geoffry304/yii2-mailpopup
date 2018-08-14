<?php

namespace geoffry304\mailpopup;

/**
 * Description of Module
 *
 * @author Geoffry Van den Eede
 */
use Yii;

/**
 * Class Module
 * @package geoffry304\mailpopup
 */
class Module extends yii\base\Module {

    public $controllerNamespace = "geoffry304\mailpopup\controllers";
    
    public $mailClass = "geoffry304\mailpopup\models\Mail";
    
     /**
     * Get object instance of model
     * @param string $name
     * @param array  $config
     * @return ActiveRecord
     */
    public function model($name, $config = [])
    {
        if ($name == "Mail"){
               $config["class"] = $this->mailClass;
        return Yii::createObject($config);
        }
     
    }

}
