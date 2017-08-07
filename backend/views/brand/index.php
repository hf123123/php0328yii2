<?=\yii\bootstrap\Html::a('添加',['brand/add'],['class'=>'btn btn-info'])?>

<table class="table table-bordered table-hover table-striped table-responsive table-condensed">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>LOGO图片</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->name?></td>
        <td><?=$model->intro?></td>
        <td><?=\yii\bootstrap\Html::img($model->logo,['height'=>50])?></td>
        <td><?=$model->sort?></td>
        <td><?=\backend\models\Brand::getstatusOption($model->status)?></td>
        <td><?=\yii\bootstrap\Html::a('删除',['brand/del','id'=>$model->id],['class'=>'btn btn-danger'])?>
            <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$model->id],['class'=>'btn btn-info'])?></td>
    </tr>
    <?php endforeach;?>
</table>
<?=\yii\widgets\LinkPager::widget(['pagination'=>$pager])?>
