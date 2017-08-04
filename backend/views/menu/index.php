<?=\yii\bootstrap\Html::a('添加',['menu/add'],['class'=>'btn btn-info'])?>
<table class="table table-bordered table-hover table-striped table-responsive table-condensed">
    <thead>
    <tr>
        <th>名称</th>
        <th>URL</th>
        <th>排序</th>
        <th>操作</th>
    </tr>

    </thead>
    <tbody>
    <?php foreach ($models as $model):?>
    <tr>
        <td><?=$model->name?></td>
        <td><?=$model->url?></td>
        <td><?=$model->sort?></td>
        <td>
            <?=\yii\bootstrap\Html::a('删除',['menu/del','id'=>$model->id],['class'=>'btn btn-danger'])?>
            <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$model->id],['class'=>'btn btn-info'])?>
        </td>
    </tr>
        <?php foreach($model->children as $child):?>
            <tr>
                <td>— —<?=$child->name?></td>
                <td><?=$child->url?></td>
                <td><?=$child->sort?></td>
                <td>
                    <?=\yii\bootstrap\Html::a('删除',['menu/del','id'=>$model->id],['class'=>'btn btn-danger'])?>
                    <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$model->id],['class'=>'btn btn-info'])?>
                </td>
            </tr>
        <?php endforeach;?>
    <?php endforeach;?>
    </tbody>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css');
$this->registerJsFile('//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs('$(".table").DataTable({
});');