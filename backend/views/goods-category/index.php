<?=\yii\bootstrap\Html::a('添加',['goods-category/add'],['class'=>'btn btn-info'])?>

<table class="table table-bordered table-hover table-striped table-responsive table-condensed">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>

        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=\yii\bootstrap\Html::a('删除',['goods-category/del','id'=>$model->id],['class'=>'btn btn-danger'])?>
                <?=\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$model->id],['class'=>'btn btn-info'])?></td>
        </tr>
    <?php endforeach;?>
</table>