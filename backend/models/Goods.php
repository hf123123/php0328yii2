<?php
namespace backend\models;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property string $goods_category_id
 * @property string $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property string $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property string $sort
 */
class Goods extends \yii\db\ActiveRecord
{
    public static $sale_options = [1=>'上架',0=>'下架'];
    public static $status_options = [1=>'正常',0=>'删除'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'goods_category_id', 'brand_id', 'market_price', 'shop_price', 'stock'], 'required'],
            [['goods_category_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort', 'create_time','view_times'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn'], 'string', 'max' => 255],
            [['logo'], 'string', 'max' => 255],
            [['sn'], 'unique'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'Logo',
            'goods_category_id' => '商品分类',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '1在售  0下架',
            'status' => '1正常 0回收站',
            'sort' => '排序',
            'inputtime' => 'Inputtime',
        ];
    }

    /*
     * 品牌选项
     */
    public static function getBrandOptions(){
        return ArrayHelper::map(Brand::find()->where(['!=','status',-1])->asArray()->all(),'id','name');
    }
    /*
     * 商品和相册关系 1对多
     */
    public function getGalleries()
    {
        return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
    }
    /*
     * 获取商品详情
     */
    public function getGoodsIntro()
    {
        return $this->hasOne(GoodsIntro::className(),['goods_id'=>'id']);
    }
    //获取图片轮播数据
    public function getPics()
    {
        $images = [];
        foreach ($this->galleries as $img){
            $images[] = Html::img($img->path);
        }
        return $images;
    }
}