<?php
$form=\yii\bootstrap\ActiveForm::begin();
//echo $form->field($model,'username');
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'newpassword')->passwordInput();
echo $form->field($model,'renewpassword')->passwordInput();
echo \yii\bootstrap\Html::submitButton('确认修改',['class'=>'btn btn-success']);
\yii\bootstrap\ActiveForm::end();