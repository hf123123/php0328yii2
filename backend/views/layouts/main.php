<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '商品后台管理',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
//    $menuItems=[
//           ['label' => '登录', 'url' => ['user/login']],//可跳转的一级菜单
//           ['label' => '用户管理', 'items' => [
//               ['label' => '添加用户', 'url' => ['user/add']],//添加用户
//               ['label' => '用户列表', 'url' => ['user/index']],//用户列表
//           ]],//不可跳转，带下来列表的一级菜单
//           ['label' => '文章管理', 'items' => [
//               ['label' => '添加文章', 'url' => ['user/add']],//添加用户
//               ['label' => '文章列表', 'url' => ['user/index']],//用户列表
//           ]],//不可跳转，带下来列表的一级菜单
//   ];
    $menuItems=[];
    $menus=backend\models\Menu::findAll(['parent_id'=>0]);
   foreach ($menus as $menu){
       $items=[];
       foreach ($menu->children as $child){
          if (Yii::$app->user->can($child->url)){
               $items[]=['label'=>$child->name,'url'=>[$child->url]];
           }
       }
       if (!empty($items)){
           $menuItems[]=['label'=>$menu->name,'items'=>$items];
       }
   }
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '登录', 'url' => ['/admin/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/admin/logout'], 'post')
            . Html::submitButton(
                '注销 (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
