<h1><?=$model->name?></h1>
<?=\yii\bootstrap\Carousel::widget([
    'items' => $model->getpics()
]);?>
<div class="container">
    <?=$model->goodsIntro->content?>
</div>
