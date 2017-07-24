<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
//echo $form->field($model,'auth_key',['inline'=>1])->radioList([1=>'是',0=>'否']);
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'email');
//echo $form->field($model,'roles')->checkboxList(\yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(),'name','description'));
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();