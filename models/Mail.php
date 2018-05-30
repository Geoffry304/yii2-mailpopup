<?php

namespace app\timedesk\models;

use yii\base\Exception;
use yii\base\Model;
use Yii;

class Mail extends Model {

//    class Appointment extends \yii\db\ActiveRecord {
    public $from;
    public $to;
    public $cc;
    public $bcc;
    public $subject;
    public $body;
    public $languagecode;
    public $class;
    public $classextra;
    public $idmodel;
    public $template;
    public $params;
    public $attachments;
    public $savedattachments;
    public $newattachments;

    public function rules() {
        return [
            [['languagecode'], 'string', 'max' => 16],
            [['from', 'to', 'cc', 'bcc', 'subject', 'body', 'attachments', 'class', 'classextra'], 'safe'],
            [['from', 'to', 'body', 'subject'], 'required'],
            [['from'], 'email'],
            [['to', 'cc', 'bcc'], 'checkEmailList']
        ];
    }

    public function checkEmailList($attribute, $params) {
        $emails = $this->$attribute;

        if (!is_array($emails)) {
            if (strpos($emails, ',') !== false) {
                $char = ",";
            } else {
                $char = ";";
            }
            $emails = explode($char, $emails);
        }
        $validator = new \yii\validators\EmailValidator;
        foreach ($emails as $email) {
            if (!$validator->validate(trim($email))) {
                $this->addError($attribute, "'$email' is not a valid email.");
            }
        }
    }

    public function attributeLabels() {
        return [
            'from' => Yii::t('app', 'From'),
            'to' => Yii::t('app', 'To'),
            'cc' => Yii::t('app', 'Cc'),
            'bcc' => Yii::t('app', 'Bcc'),
            'subject' => Yii::t('app', 'Subject'),
            'body' => Yii::t('app', 'Body'),
        ];
    }

    public function toMailFormat() {
//        $explode = explode("<", $this->from);
//        $this->from = [$explode[0], str_replace(">","", $explode[1])];
        $this->from = self::toArrayForm($this->from);
        $this->to = self::toArrayForm($this->to);
        $this->cc = self::toArrayForm($this->cc);
        $this->bcc = self::toArrayForm($this->bcc);
    }

    public static function toArrayForm($emails) {
        if (empty($emails)) {
            return null;
        }
        if (strpos($emails, ',') !== false) {
            $char = ",";
        } else {
            $char = ";";
        }
        if (strpos($emails, $char)) {
            $explode = explode($char, $emails);
            $array = [];
            foreach ($explode as $e) {
                array_push($array, trim($e));
            }
            return $array;
        } else {
            return $emails;
        }
    }

    public function sendMail() {
        $this->toMailFormat();
        $mail = Yii::$app->mailer->compose("$this->languagecode/html", ['subject' => $this->subject, 'content' => $this->body])
//        $mail = Yii::$app->mailer->compose()
                ->setFrom($this->from)
                ->setTo($this->to);
        if ($this->savedattachments) {
            foreach ($this->savedattachments['attachments'] as $attachment) {
                if ($attachment[0] == "/") {
                    $mail->attach(Yii::getAlias("@webroot") . $attachment);
                } else {
                    $mail->attach(Yii::getAlias("@webroot") . "/" . $attachment);
                }
            }
        }
        if ($this->newattachments) {
            foreach ($this->newattachments as $attachment) {
                if ($attachment[0] == "/") {
                    $mail->attach(Yii::getAlias("@webroot") . $attachment);
                } else {
                    $mail->attach(Yii::getAlias("@webroot") . "/" . $attachment);
                }
            }
        }
        ($this->cc) ? $mail->setCc($this->cc) : '';
        ($this->bcc) ? $mail->setBcc($this->bcc) : '';

        $mail->setSubject($this->subject)
                ->send();

        if ($mail) {
//            if ($this->newattachments) {
//                foreach ($this->newattachments as $attachment) {
//                    if (file_exists($attachment)) {
//                        unlink($attachment);
//                    }
//                }
//            }
//            if (strpos($this->template,"PRODUCTORDER") !== false && $this->savedattachments) {
//                foreach ($this->savedattachments['attachments'] as $attachment) {
//                    if (file_exists($attachment)) {
//                        unlink($attachment);
//                    }
//                }
//            }
            ($this->class) ? $this->doExtra() : '';
            return true;
        } else {
            return false;
        }
    }

    public function doExtra() {
        $class = new $this->class;

        if ($class instanceof \app\modules\ticketing\models\Ticket) {
            $model = \app\modules\ticketing\models\Ticket::findOne($this->idmodel);
            if ($this->classextra == "sendpdf") {
                $model->onsite->ServiceReportSent = 1;
                $model->onsite->save();
            } else {
                $model->ConfEmailSent = 1;
            }
            $model->save();
        } else if ($class instanceof \app\modules\productregister\models\Productorder) {
            $orders = explode(',', $this->idmodel);
            foreach ($orders as $order) {
                $model = \app\modules\productregister\models\Productorder::findOne($order);
                $model->ReminderCount++;
                $model->save();
            }
        }
    }

    public function setCorrectTemplate() {
        $template = Yii::$app->get('templateManager')->getTemplate($this->template, $this->languagecode);
        $template->parse($this->params);
        $this->body = $template->body;
        $this->subject = $template->subject;
    }

    public function addNewAttachments() {
        $this->newattachments = \yii\web\UploadedFile::getInstances($this, 'attachments');
        $directory = 'uploads/temp';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $array = [];
        foreach ($this->newattachments as $attachment) {
            $url = $directory . "/" . $attachment->baseName . "." . $attachment->extension;
            $attachment->saveAs($url);
            array_push($array, $url);
        }
        $this->newattachments = $array;
    }

    public static function getEmails($companyid = null) {
        $andwhere = ($companyid) ? ['CompanyId' => $companyid] : "";
        $companyuser = Companyuser::find()->asArray()->where('UserTypeId !=' . Usertype::getUserTypeId(Usertype::TYPE_INACTIVE))->andWhere($andwhere)->all();
        return \yii\helpers\ArrayHelper::map($companyuser, 'Email', 'Email');
    }

}
