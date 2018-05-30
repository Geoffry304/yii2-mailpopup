<?php

namespace app\timedesk;

/**
 * Description of MailPopupWidget
 *
 * @author Geoffry Van den Eede
 */
use Yii;
use yii\base\InvalidConfigException;


class MailPopupWidget extends yii\base\Widget {
    
    const PLUGIN_NAME = 'Mailpopup';
    
    public $template;
    public $body;
    public $subject;
    public $languagecode;
    public $from;
    public $to;
    public $bcc;
    public $params;
    public $options;
    public function init(){
        if (!isset($this->template) && (!isset($this->body) && !isset($this->subject))){
            throw new InvalidConfigException("You must set template or body & subject parameter");
        }
        if (!isset($this->languagecode)){
             $this->languagecode = Yii::$app->language;
        }
        
        if (!isset($this->options['text'])){
            $this->options['text'] = '<i class="fa fa-envelope-o"></i> ' . Yii::t('app', 'MAIL');
        }
        if (!isset($this->options['class'])){
            $this->options['class'] = "btn btn-default btnmailpdf";
        }
        
        if (!isset($this->options['id'])){
            $this->options['id'] = "btnmailpdf";
        }
        
        parent::init();
    }
    
    public function run() {
        parent::run();
        
        echo $this->getOutput();
    }
    
    public function getOutput(){
        $output = \yii\helpers\Html::a($this->options['text'], 
                  \yii\helpers\Url::to(["#",
                  'to' => $this->to,
                  'from' => $this->from,
                  'bcc' => $this->bcc,
                  'subj' => $this->subject,
                  'body' => $this->body,
                  'template' => $this->template,
                  'params' => $this->params,
                  'languagecode' => $this->languagecode,
                  
            ]),
            ['role' => 'modal-remote', 'title' => 'Send mail', 'class' => $this->options['class'],'id' => $this->options['id'], 'data-language' => $this->languagecode]
            );
        
        return $output;
        
        
    }
    
    
    
}

