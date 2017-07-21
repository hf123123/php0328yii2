<?=\yii\bootstrap\Html::a('添加',['manage/add'],['class'=>'btn btn-info'])?>

<table class="table table-bordered table-hover table-striped table-responsive table-condensed">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>文章分类</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->article_category_id?></td>
            <td><?=$model->sort?></td>
            <td><?=\backend\models\Manage::statusOption($model->status)?></td>

            <td><?=date('Y-m-d H:i:s',$model->create_time)?></td>

            <td><?=\yii\bootstrap\Html::a('删除',['manage/delete','id'=>$model->id],['class'=>'btn btn-danger'])?>
                <?=\yii\bootstrap\Html::a('修改',['manage/edit','id'=>$model->id],['class'=>'btn btn-info'])?></td>
        </tr>
    <?php endforeach;?>
</table>
