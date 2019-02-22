<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\redis\ActiveQuery;

/**
 * This is the model class for table "{{%banner}}".
 *
 * @property string $id
 * @property string $title      宣传页名称
 * @property string $path       内容路径
 * @property string $link       超联接
 * @property string $target     打开方式：_blank,_self,_parent,_top
 * @property int $type          内容类型：1图片，2视频
 * @property int $sort_order    排序
 * @property int $is_publish    是否发布：0否 1是
 * @property string $des        描述
 * @property string $created_by 创建人ID
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * 
 * @property AdminUser $adminUser   创建人
 */
class Banner extends ActiveRecord
{
    /** 内容-图片 */
    const TYPE_IMG = 1;
    /** 内容-视频 */
    const TYPE_VIDEO = 2;
    
    /** 发布状态-未发布 */
    const NO_PUBLISH = 0;
    /** 发布状态-已发布 */
    const YES_PUBLISH = 1;
    
    /** 打开方式-新开页面 */
    const TARGET_BLANK = '_blank';
    /** 打开方式-替换打开 */
    const TARGET_SELF = '_self';

    /**
     * 内容类型
     * @var array
     */
    public static $contentType = [
        self::TYPE_IMG => '图片',
        self::TYPE_VIDEO => '视频',
    ];
    
    /**
     * 发布状态
     * @var array 
     */
    public static $publishStatus = [
        self::NO_PUBLISH => '未发布',
        self::YES_PUBLISH => '已发布',
    ];

    /**
     * 打开方式
     * @var array 
     */
    public static $targetType = [
        self::TARGET_BLANK => '新开页面',
        self::TARGET_SELF => '替换打开',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%banner}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'sort_order', 'is_publish', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['path', 'link', 'des'], 'string', 'max' => 255],
            [['target'], 'string', 'max' => 10],
            [['created_by'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'path' => Yii::t('app', 'Path'),
            'link' => Yii::t('app', 'Link'),
            'target' => Yii::t('app', 'Target'),
            'type' => Yii::t('app', 'Type'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'is_publish' => Yii::t('app', 'Is Publish'),
            'des' => Yii::t('app', 'Des'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * 关联获取创建者
     * @return ActiveQuery
     */
    public function getAdminUser()
    {
        return $this->hasOne(AdminUser::class, ['id' => 'created_by']);
    }
}
