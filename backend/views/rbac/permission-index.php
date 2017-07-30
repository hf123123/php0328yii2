<?=\yii\bootstrap\Html::a('添加',['rbac/add-permission'],['class'=>'btn btn-info'])?>

<table class="table table-bordered table-hover table-striped table-responsive table-condensed">
    <thead>
    <tr>
            <th>名称</th>
            <th>描述</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($models as $model):?>
            <tr>
                <td><?=$model->name?></td>
                <td><?=$model->description?></td>
                <td><?=\yii\bootstrap\Html::a('修改',['rbac/edit-permission','name'=>$model->name],['class'=>'btn btn-info'])?>
                    <?=\yii\bootstrap\Html::a('删除',['rbac/del-permission','name'=>$model->name],['class'=>'btn btn-danger'])?></td>
            </tr>
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
