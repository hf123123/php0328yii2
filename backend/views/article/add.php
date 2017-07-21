<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo  $form->field($model,'name');
echo  $form->field($model,'intro')->textarea();

echo  $form->field($model,'sort')->fileInput(['type'=>'number']);
echo  $form->field($model,'status',['inline'=>true])->radioList(\backend\models\Brand::getstatusOptions());
echo \yii\bootstrap\Html::submitButton('确定',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
