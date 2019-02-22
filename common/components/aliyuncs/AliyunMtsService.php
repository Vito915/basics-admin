<?php

namespace common\components\aliyuncs;

use common\models\media\Media;
use Yii;
use yii\base\Object;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%aliyun_mts_service}}".
 *
 * @property string $id
 * @property string $request_id 请求ID
 * @property string $job_id     任务ID
 * @property string $media_id   媒体ID，关联media表id字段
 * @property int $is_finish     是否完成：0否 1是
 * @property int $is_del        是否删除：0否 1是
 * @property int $level         视频质量1~9
 * @property int $result        结果：0失败 1成功
 * @property string $created_by 任务创建人
 * @property string $created_at 创建时间
 * @property string $updated_at 更前时间
 *
 * @property Media $media
 */
class AliyunMtsService extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%aliyun_mts_service}}';
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
            [['media_id', 'is_finish', 'is_del', 'level', 'result', 'created_at', 'updated_at'], 'integer'],
            [['request_id'], 'string', 'max' => 255],
            [['job_id', 'created_by'], 'string', 'max' => 32],
            [['media_id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::className(), 'targetAttribute' => ['media_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'request_id' => Yii::t('app', 'Request ID'),
            'job_id' => Yii::t('app', 'Job ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'is_finish' => Yii::t('app', 'Is Finish'),
            'is_del' => Yii::t('app', 'Is Del'),
            'level' => Yii::t('app', 'Level'),
            'result' => Yii::t('app', 'Result'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * 获取已完成转码文件等级
     * 
     * @param string $media_id      视频ID
     * @return array [0,1,2,3]
     */
    public static function getFinishLevel($media_id) {
        $hasDoneLevels = AliyunMtsService::find()
                        ->select('level')
                        ->from(AliyunMtsService::tableName())
                        ->where([
                            'media_id' => $media_id,
                            'is_del' => 0,
                            'is_finish' => 1,
                            'result' => 1,
                        ])->column();
        return $hasDoneLevels;
    }

    /**
     * 从反馈里批量添加记录
     * 
     * @param string $media_id      视频ID
     * @param Object $response      反馈数据
     */
    public static function batchInsertServiceForMts($media_id, $response) {
        //保存调用记录，用于完成核实
        $rows = [];
        $time = time();
        $request_id = $response->RequestId;
        foreach ($response->JobResultList->JobResult as $JobResult) {
            $userData = json_decode($JobResult->Job->Output->UserData);
            $rows [] = [
                $request_id,                        //请求ID
                $JobResult->Job->JobId,             //任务ID
                $media_id,                          //视频ID
                $JobResult->Success ? 0 : 1,        //提交任务是否成功
                $userData->level,                   //转码的等级
                \Yii::$app->user->id,               //转码操作人，当前用户ID
                $time, $time                        //创建、更新时间
            ];
        }
        \Yii::$app->db->createCommand()->batchInsert(AliyunMtsService::tableName(), ['request_id', 'job_id', 'media_id', 'is_finish', 'level', 'created_by', 'created_at', 'updated_at'], $rows)->execute();
    }

    
    /**
     * @return ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasOne(Media::className(), ['id' => 'media_id']);
    }
}
