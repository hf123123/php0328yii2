<?php echo \yii\bootstrap\Html::a('添加管理员',['admin/add'],['class'=>'btn btn-success'])?>&nbsp;
<?php echo \yii\bootstrap\Html::a('注销',['admin/login'],['class'=>'btn btn-warning'])?>&emsp;
<?php echo \yii\bootstrap\Html::a('修改密码',['admin/chpw'],['class'=>'btn btn-info'])?>


<table class="table table-bordered table-hover table-striped table-responsive table-condensed">
        <tr>
            <th>ID</th>
            <th>用户名</th>
            <!--  <th>身份</th>-->
            <th>邮箱</th>
            <th>创建时间</th>
            <th>最后登录时间</th>
            <th>最后登录IP</th>

            <!--  <th>更新时间</th>-->
            <th>操作</th>
        </tr>

        <?php foreach ($models as $model):?>
            <tr>
                <td><?=$model->id?></td>
                <td><?=$model->username?></td>

                <td><?=$model->email?></td>

                <!--<td><?/*=$model->status*/?></td>-->
                <td><?=date('Y-m-d H:i:s',$model->created_at)?></td>
                <td><?=date('Y-m-d H:i:s',$model->last_login_time)?></td>
                <!--<td><?/*=date('Y-m-d H:i:s',$model->updated_at)*/?></td>-->

                <td><?=$model->last_login_ip?></td>
                <td>
                    <?=\yii\bootstrap\Html::a('删除',['admin/delete','id'=>$model->id],['class'=>'btn btn-danger'])?>
                    <?=\yii\bootstrap\Html::a('修改',['admin/edit','id'=>$model->id],['class'=>'btn btn-info'])?>
                </td>
            </tr>
        <?php endforeach;?>

    </table>
