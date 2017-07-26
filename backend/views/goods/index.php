<?=\yii\bootstrap\Html::a('添加',['goods/add'],['class'=>'btn btn-info'])?>

<?php
$form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'get',
    //get方式提交,需要显式指定action
    'action'=>\yii\helpers\Url::to(['goods/index']),
    'layout'=>'inline'
]);
echo $form->field($model,'name')->textInput(['placeholder'=>'商品名'])->label(false);
echo $form->field($model,'sn')->textInput(['placeholder'=>'货号'])->label(false);
echo $form->field($model,'minPrice')->textInput(['placeholder'=>'￥'])->label(false);
echo $form->field($model,'maxPrice')->textInput(['placeholder'=>'￥'])->label('-');
echo \yii\bootstrap\Html::submitButton('<span class="glyphicon glyphicon-search"></span>搜索',['class'=>'btn btn-default']);
\yii\bootstrap\ActiveForm::end();
?>


    <table class="table table-bordered table-hover table-striped table-responsive table-condensed">
    <tr>
        <th>ID</th>
      <th>名称</th>
        <th>货号</th>
        <th>LOGO</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>是否在售</th>
        <th>状态</th>
        <th>排序</th>
        <th>添加时间</th>

        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->sn?></td>
            <td><?=\yii\bootstrap\Html::img($model->logo,['height'=>50])?></td>

            <td><?=$model->brand_id?></td>
            <td><?=$model->shop_price?></td>
            <td><?=$model->stock?></td>
            <td><?=$model->is_on_sale=1?'上架':'下架';?></td>
            <td><?=$model->status=1?'正常':'回收';?></td>

            <td><?=$model->sort?></td>
            <td><?=date('Y-m-d H:i:s',$model->create_time)?></td>

            <td>
                <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-picture"></span>相册',['gallery','id'=>$model->id],['class'=>'btn btn-default'])?>
                <?=\yii\bootstrap\Html::a('删除',['goods/delete','id'=>$model->id],['class'=>'btn btn-danger'])?>
                <?=\yii\bootstrap\Html::a('修改',['goods/edit','id'=>$model->id],['class'=>'btn btn-info'])?>

                </td>
        </tr>
    <?php endforeach;?>
</table>
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager
])?>

