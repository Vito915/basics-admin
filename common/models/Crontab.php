<?php

namespace common\models;

use common\utils\CronParser;
use Exception;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%crontab}}".
 *
 * @property int $id
 * @property string $name                               定时任务名称
 * @property string $route                              任务路由
 * @property string $crontab_str                        crontab格式
 * @property string $last_rundate                       任务上次运行时间
 * @property string $next_rundate                       任务下次运行时间
 * @property string $exec_memory                        任务执行消耗内存(单位/byte)
 * @property string $exec_time                          任务执行消耗时间
 * @property int $status                                任务运行状态 0正常 1任务报错
 * @property int $is_del                                任务开关 0关闭 1开启
 * @property string $created_at                         创建时间
 * @property string $updated_at                         更新时间
 */
class Crontab extends ActiveRecord {

    /**
     * switch字段的文字映射
     * @var array
     */
    private $switchTextMap = [
        0 => '关闭',
        1 => '开启',
    ];

    /**
     * status字段的文字映射
     * @var array
     */
    private $statusTextMap = [
        0 => '正常',
        1 => '任务保存',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%crontab}}';
    }

    public function behaviors() {
        return [TimestampBehavior::class];
    }

    /**
     * {@inheritdoc}
     * 偷偷和你说个
     */
    public function rules() {
        return [
            [['name', 'route', 'crontab_str'], 'required'],
            [['last_rundate', 'next_rundate'], 'safe'],
            [['exec_memory', 'exec_time'], 'number'],
            [['status', 'is_del', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['route'], 'string', 'max' => 255],
            [['crontab_str'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'route' => Yii::t('app', 'Route'),
            'crontab_str' => Yii::t('app', 'Crontab Str'),
            'last_rundate' => Yii::t('app', 'Last Rundate'),
            'next_rundate' => Yii::t('app', 'Next Rundate'),
            'exec_memory' => Yii::t('app', 'Exec Memory'),
            'exec_time' => Yii::t('app', 'Exec Time'),
            'status' => Yii::t('app', 'Status'),
            'is_del' => Yii::t('app', 'Is Del'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * 获取switch字段对应的文字
     * @author jlb
     * @return ''|string
     */
    public function getSwitchText() {
        if (!isset($this->switchTextMap[$this->is_del])) {
            return '';
        }
        return $this->switchTextMap[$this->is_del];
    }

    /**
     * 获取status字段对应的文字
     * @author jlb
     * @return ''|string
     */
    public function getStatusText() {
        if (!isset($this->statusTextMap[$this->status])) {
            return '';
        }
        return $this->statusTextMap[$this->status];
    }

    /**
     * 计算下次运行时间
     * @author jlb
     */
    public function getNextRunDate() {
        if (!CronParser::check($this->crontab_str)) {
            throw new Exception("格式校验失败: {$this->crontab_str}", 1);
        }
        return CronParser::formatToDate($this->crontab_str, 1)[0];
    }

}
