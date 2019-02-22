<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%watermark}}".
 *
 * @property string $id
 * @property int $type                                                          水印类型：1图片 2文字（预留）
 * @property string $name                                                       水印名称
 * @property string $url                                                        图片路径
 * @property string $oss_key                                                    oss名称
 * @property string $width                                                      水印宽
 * @property string $height                                                     水印高
 * @property string $dx                                                         水平偏移位置
 * @property string $dy                                                         垂直偏移位置
 * @property string $refer_pos                                                  水印位置，值范围TopRight、TopLeft、BottomRight、BottomLeft
 * @property int $is_del                                                        是否删除：0否 1是
 * @property int $is_selected                                                   默认选中：0否 1是
 * @property string $created_at                                                 创建时间
 * @property string $updated_at                                                 更新时间
 */
class Watermark extends ActiveRecord
{
    /** 状态-全部 */
    const STATUS_ALL = '';
    
    /** 状态-启用 */
    const STATUS_ENABLE = 0;
    
    /** 状态-停用 */
    const STATUS_STOP = 1;
    
    /** 位置-右上 */
    const POSITION_TOPRIGHT = 'TopRight';
    
    /** 位置-左上 */
    const POSITION_TOPLEFT = 'TopLeft';
    
    /** 位置-右下 */
    const POSITION_BOTTOMRIGHT = 'BottomRight';
    
    /** 位置-左下 */
    const POSITION_BOTTOMLEFT = 'BottomLeft';

    /**
     * 水印状态
     * @var array 
     */
    public static $statusMap = [
        self::STATUS_ENABLE => '启用',
        self::STATUS_STOP => '停用'
    ];
    /**
     * 水印位置
     * @var array 
     */
    public static $referPosMap = [
        self::POSITION_TOPRIGHT => '右上',
        self::POSITION_TOPLEFT => '左上',
        self::POSITION_BOTTOMRIGHT => '右下',
        self::POSITION_BOTTOMLEFT => '左下'
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%watermark}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors() 
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type', 'is_del', 'is_selected', 'created_at', 'updated_at'], 'integer'],
            [['width', 'height', 'dx', 'dy'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['url', 'oss_key'], 'string', 'max' => 255],
            [['refer_pos'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'oss_key' => Yii::t('app', 'Oss Key'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
            'dx' => Yii::t('app', 'Dx'),
            'dy' => Yii::t('app', 'Dy'),
            'refer_pos' => Yii::t('app', 'Refer Pos'),
            'is_del' => Yii::t('app', 'Is Del'),
            'is_selected' => Yii::t('app', 'Is Selected'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * 获取已启用的所有水印图
     * @param integer|array $id
     * @return array
     */
    public static function getEnabledWatermarks($id = null)
    {
        //查询水印图
        $query = self::find()->from(['Watermark' => self::tableName()]);
        
        // 所需要的字段
        $query->select([
            'Watermark.id', 'Watermark.width', 'Watermark.height', 'Watermark.dx',
            'Watermark.dy', 'Watermark.refer_pos', 'Watermark.url', 
            "if(Watermark.is_selected = 1, 1, 0) AS is_selected"
        ]);        
        
        //必要条件
        $query->where(['Watermark.is_del' => 0]);
        
        // 按id查询
        $query->andFilterWhere(['Watermark.id' => $id]);
        
        return $query->asArray()->all();
    }
    
    /**
     * 按条件探索转码水印配置
     * 
     * @return array [[InputFile,Dx,Dy,Width,Height,ReferPos],[]] 
     */
    public static function findAllForMts($condition) {
        /* @var $cw CustomerWatermark */
        $result = self::find()->where($condition)->all();
        $cws = [];
        foreach ($result as $cw) {
            $cw_t = [
                'InputFile' => [
                    'Object' => urldecode($cw->oss_key),      //水印输入文件名
                ],
                'Dx' => self::valuable($cw->dx),    //水平偏移
                'Dy' => self::valuable($cw->dy),    //垂直偏移
                'ReferPos' => $cw->refer_pos,                       //位置
            ];
            if($cw->width != 0){
                $cw_t['Width'] = self::valuable($cw->width);        //宽;
            }
            if($cw->height != 0){
                $cw_t['Height'] = self::valuable($cw->height);      //高
            }
            
            $cws []= $cw_t;
        }
        
        return $cws;
    }
    
    /**
     * 验证数字 (0,1)[8,4096]
     * @param type $value
     */
    private static function valuable($value) {
        if ($value < 8) {
            $value = $value <= 0 ? $value = 0.13 : $value;
            $value = $value > 1 ? $value = 1 : $value;
        } else {
            $value = $value > 4096 ? $value = 4096 : intval($value);
        }
        return $value;
    }
}
