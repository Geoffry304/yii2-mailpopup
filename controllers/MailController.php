<?php

namespace geoffry304\mailpopup\controllers;

use Yii;
use yii\web\Controller;
use app\models\Mail;
use \yii\web\Response;
use yii\helpers\Html;
use app\models\Usertype;

class MailController extends Controller {

    public function actionCreate() {
        $request = Yii::$app->request;
        $model = new Mail();

        
        if ($request->isAjax) {
//            foreach (Yii::$app->params['fromMail'] as $key => $mail){
//                $model->from = $mail. "<$key>";
//                $model->cc = $key;
//            }
            //            $model->from = "dispatch@dvit.be";
            $model->from = ($request->get('from')) ? $request->get('from') : "dispatch@dvit.be";
             if ($request->get('bcc')){
                   $model->bcc = $request->get('bcc');
             } else {
                 $model->cc = $model->from; 
             }
             
             $companyid = ($request->get('companyid')) ? $request->get('companyid'): null;
//            $model->cc = $model->from;
            $model->to = $request->get('to');
            $model->subject = $request->get('subj');
            $model->body = $request->get('body');
            $model->languagecode = $request->get('languagecode');
            $model->class = $request->get('class');
            $model->classextra = $request->get('classextra');
            $model->idmodel = $request->get('idmodel');
            $model->template = $request->get('template');
            $model->params = $request->get('params');
            $model->savedattachments = $request->get('attachments');
            if ($model->template) {
                $model->setCorrectTemplate();
            }
//            if ($model->savedattachments) {
//                $array['initialPreview'] = [];
//                $array['initialPreviewConfig'] = [];
//                $array['attachments'] = [];
//                foreach ($model->savedattachments as $key => $attachtment) {
//                    $fileinfo = pathinfo($attachtment);
//                    $arrayinfo = [];
//                    if ($fileinfo['extension'] == "pdf") {
//                        $arrayinfo['type'] = "pdf";
//                    }
//                    
//                    $arrayinfo['url'] =  \yii\helpers\Url::to(['/mail/filedelete']);
//                    $arrayinfo['key'] = $key;
//                    $url = Yii::getAlias('@web') . "/" . $attachtment;
//                    array_push($array['attachments'], $attachtment);
//                    array_push($array['initialPreview'], $url);
//                    array_push($array['initialPreviewConfig'], $arrayinfo);
//                }
//                $model->savedattachments = $array;
//            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('app', 'Stel mail op'),
                    'content' => $this->renderAjax('create', ['model' => $model, 'companyid' => $companyid]),
                    'footer' => Html::button(Yii::t('app', 'Sluiten'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::button(Yii::t('app', 'Verstuur'), ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) && $model->validate()) {
//                  throw new \Exception("<pre>" . print_r($model->savedattachments, true) . "</pre>");
                 $model->addNewAttachments();
                 $model->sendMail();
                return [
                    'forceReload' => '#buttons-div',
                    'forceClose' => true,
                    'title' => 'Compose mail',
                    'content' => '<span class="text-success">Create appointment success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Create More', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => "Create new appointment",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'companyid' => $companyid
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::button('Send', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        } else {
            /*
             *   Process for non-ajax request
             */
            if ($model->load($request->post())) {
                return $this->redirect(['ticketing/ticket']);
            } else {
                return $this->render('create', [
                            'model' => $model,
                ]);
            }
        }
    }
    
     public function actionFiledelete(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        echo ["test"=>"test"];		
    }
    
    public function actionEmaillist($q = null, $id = null) {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $out = ['results' => ['id' => '', 'text' => '']];
    if (!is_null($q)) {
        $query = new \yii\db\Query;
        $query->select('Email AS id, Email AS text')
            ->from('companyuser')
            ->where(['like', 'Email', $q])
            ->andWhere('UserTypeId !='. Usertype::getUserTypeId(Usertype::TYPE_INACTIVE))
            ->limit(20);
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out['results'] = array_values($data);
    }
    elseif ($id > 0) {
        $out['results'] = ['id' => $id, 'text' => \app\models\Companyuser::find($id)->Email];
    }
    return $out;
}
    

}
