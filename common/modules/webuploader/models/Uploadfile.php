<?php

namespace common\modules\webuploader\models;

use common\components\aliyuncs\Aliyun;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%uploadfile}}".
 *
 * @property int $id                        文件ID
 * @property string $name                   文件名
 * @property string $md5                    文件md5值
 * @property string $path                   文件路径
 * @property string $thumb_url              缩略图路径
 * @property string $app_id                 应用ID
 * @property string $size                   大小B
 * @property string $ext                    拓展名/后缀名
 * @property string $oss_key                oss名称/文件名
 * @property int $oss_upload_status         上传状态：0未上传，1上传中，2已上传
 * @property string $metadata               文件元数据（json格式） 包括duration、width、height、bitrate
 * @property int $is_del                    是否已经删除标记：0未删除，1已删除
 * @property string $created_by             上传人
 * @property string $created_at             创建时间
 * @property string $updated_at             更新时间
 */
class Uploadfile extends ActiveRecord {

    /* 未上传 */
    const OSS_UPLOAD_STATUS_NO = 0;
    /* 已上传 */
    const OSS_UPLOAD_STATUS_YES = 1;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%uploadfile}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['md5'], 'required'],
            [['size', 'is_del', 'oss_upload_status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'path', 'thumb_url', 'oss_key'], 'string', 'max' => 255],
            [['metadata'], 'string', 'max' => 500],
            [['ext'], 'string', 'max' => 10],
            [['id', 'md5'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'md5' => Yii::t('app', 'Md5'),
            'path' => Yii::t('app', 'Path'),
            'thumb_url' => Yii::t('app', 'Thumb Path'),
            'size' => Yii::t('app', 'Size'),
            'ext' => Yii::t('app', 'Ext'),
            'is_del' => Yii::t('app', 'Is Del'),
            'oss_key' => Yii::t('app', 'OSS Key'),
            'oss_upload_status' => Yii::t('app', 'OSS Upload Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * 上传到阿里云
     * 
     * @param string $key           文件名称，默认=customer_id/user_id/file_id.[ext]
     * @return array [success,msg]
     */
    public function uploadOSS($key = null) {
        if ($key == null) {
            //生成文件名，当oss_key不为空时，将使用oss_key作为文件名
            $config = isset(Yii::$app->params['webuploader']) ? Yii::$app->params['webuploader'] : ['savePath' => 'upload/webuploader/files'];
            $filename = $config['savePath'] . pathinfo($this->path, PATHINFO_BASENAME);
            //设置文件名
            $object_key = $this->oss_key == '' ? $filename : $this->oss_key;
        } else {
            $object_key = $key;
        }
        $object_key_path_info = pathinfo($object_key);
        $thumb_key = "{$object_key_path_info['dirname']}/{$object_key_path_info['filename']}_thumb.jpg";

        try {
            //上传文件
            Aliyun::getOss()->multiuploadFile($object_key, $this->path);
            //上传缩略图
            if ($this->thumb_url != '') {
                Aliyun::getOss()->multiuploadFile($thumb_key, $this->thumb_url);
                @unlink($this->thumb_url);
                $this->thumb_url = $thumb_key . "?rand=" . rand(0, 999);
            }
            //更新数据
            $this->oss_upload_status = Uploadfile::OSS_UPLOAD_STATUS_YES;
            $this->oss_key = $object_key;

            $this->save(false, ['oss_upload_status', 'oss_key', 'thumb_url']);
            //删除本地文件
            @unlink($this->path);
        } catch (\Exception $ex) {
            throw new Exception('UploadOSS Failed! ' . $ex->getMessage());
        }
    }

    /**
     * 返回处理后数组
     * 
     * thumb_url和 oss_key 转为绝对路径
     * metadata 转为 array
     * 
     * @return array
     */
    public function toProcessedArray(){
        $arr = $this->toArray();
        if($arr['thumb_url'] != ''){
            $arr['thumb_url'] = Aliyun::absolutePath($arr['thumb_url']);
        }
        $arr['url'] = ($arr['oss_key'] == "" ? "" : Aliyun::absolutePath($arr['oss_key']));
        $arr['metadata'] = json_decode($arr['metadata'],true);
        return $arr;
    }
}
