<?php
use yii\web\JsExpression;
$form=\yii\bootstrap\ActiveForm::begin();
echo  $form->field($model,'name');
echo \yii\bootstrap\Html::img($model->logo?$model->logo:false,['id'=>'img','height'=>50]);
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['goods/s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    //console.log(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将图片的地址赋值给logo字段
        $("#goods-logo").val(data.fileUrl);
        //将上传成功的图片回显
        $("#img").attr('src',data.fileUrl);
    }
}
EOF
        ),
    ]
]);
echo $form->field($model,'goods_category_id')->hiddenInput();
echo '<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>';
echo $form->field($model,'brand_id')->dropDownList(\backend\models\Manage::getManagOptions());
echo  $form->field($model,'market_price');
echo  $form->field($model,'shop_price');
echo  $form->field($model,'stock');
//echo  $form->field($model,'is_on_sale');
echo  $form->field($model,'is_on_sale',['inline'=>true])->radioList([1=>'上架',0=>'下架']);
echo  $form->field($model,'status',['inline'=>true])->radioList(\backend\models\Brand::getstatusOptions());
echo  $form->field($model,'sort')->fileInput(['type'=>'number']);
echo $form->field($introModel,'content')->widget('kucha\ueditor\UEditor',[]);
echo \yii\bootstrap\Html::submitButton('确定',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
//调用视图的方法加载静态资源
//加载css文件
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
//加载js文件
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
//加载js代码
//$nodes = '[
//            {id:1, pId:0, name: "父节点1"},
//            {id:11, pId:1, name: "子节点1"},
//            {id:12, pId:1, name: "子节点2"}
//        ];';
$categories[] = ['id'=>0,'parent_id'=>0,'name'=>'顶级分类','open'=>1];
$nodes = \yii\helpers\Json::encode($categories);
$nodeId = $model->goods_category_id;
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
		        onClick: function(event, treeId, treeNode){
		            //console.log(treeNode.id);
		            //将当期选中的分类的id，赋值给parent_id隐藏域
		            $("#goods-goods_category_id").val(treeNode.id);
		        }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};
  
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        zTreeObj.expandAll(true);//展开全部节点
        
        //获取节点
        var node = zTreeObj.getNodeByParam("id", "{$nodeId}", null);
        //选中节点
        zTreeObj.selectNode(node);
        //触发选中事件
        
JS
));