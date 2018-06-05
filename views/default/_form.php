<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use vova07\imperavi\Widget as ImperaviRedactor;

/* @var $this yii\web\View */
/* @var $model app\models\Mail */
/* @var $form kartik\form\ActiveForm */
?>


<div class="mail-form">


    <?php $form = ActiveForm::begin(); ?>  
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'from', ['addon' => ['prepend' => ['content' => Yii::t('app', 'From:')]]])->label(false) ?>  
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'to', ['addon' => ['prepend' => ['content' => Yii::t('app', 'To:')]]])->label(false) ?>  
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'cc', ['addon' => ['prepend' => ['content' => Yii::t('app', 'Cc:')]]])->label(false) ?>  
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'bcc', ['addon' => ['prepend' => ['content' => Yii::t('app', 'Bcc:')]]])->label(false) ?>  
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'subject', ['addon' => ['prepend' => ['content' => Yii::t('app', 'Subj:')]]])->label(false) ?>  
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'body')->widget(ImperaviRedactor::class, [
                'settings' => [
                    'minHeight' => 150
                ]
            ]) ?>
            <?php
//            echo $form->field($model, 'body')->widget(app\timedesk\FroalaEditorWidget::className(), [
//                'options' => [// html attributes
//                // 'id' => 'body'
//                ],
//                'uploadOptions' => [
//                    'parent' => 'ticket',
//                    'scheme' => true,
////            'parentid' => ''
//                ],
//                'clientOptions' => [
//                    'height' => 200,
////            'zIndex' => 9999
//                ],
//            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            echo '<label class="control-label">' . Yii::t('app', 'Bijlagen') . '</label>';
            echo kartik\widgets\FileInput::widget([
                'model' => $model,
                'attribute' => 'attachments[]',
                'options' => ['multiple' => true],
                'pluginOptions' => [
                    'showUpload' => false,
                    'showRemove' => false,
                    'initialPreview' => !empty($model->savedattachments) ? $model->savedattachments['initialPreview'] : [],
                    'initialPreviewConfig' => !empty($model->savedattachments) ? $model->savedattachments['initialPreviewConfig'] : null,
                    'initialPreviewAsData' => true,
                    'overwriteInitial' => false,
                ],
            ]);
            ?>
        </div>
    </div>

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?php // Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])   ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>
</div>

<?php $this->registerJs("$('#ajaxCrudModal').removeAttr('tabindex');", yii\web\View::POS_READY);
?>
