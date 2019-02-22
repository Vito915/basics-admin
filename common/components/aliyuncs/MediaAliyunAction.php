<?php

namespace common\components\aliyuncs;

use common\components\aliyuncs\Aliyun;
use common\models\media\Media;
use common\models\media\MediaDetail;
use common\models\media\MediaType;
use common\models\media\VideoUrl;
use common\models\Watermark;
use common\modules\webuploader\models\Uploadfile;
use common\utils\EefileUtils;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * 集合 Media Aliyun 操作
 *
 * @author wskeee
 */
class MediaAliyunAction {

    /**
     * 添加视频资源转码任务 <br/>
     * 
     * 转码流程：<br/>
     * 1、提交4种格式转码请求，添加4种格式的 MtsService 记录 <br/>
     * 2、每一种格式转码完成后回调,设置该格式 MtsService 完成，AliyunMtsController::actionTaskComplete();<br/>
     * 3、该检查4种格式是否都已完成 ：self::integrateVideoTrancode(); 所有格式转码完成后：<br/>
     *  4.1、为每一种格式添加对应的 VideoUrl 数据<br/>
     *  4.2、删除旧的 Media 与 VideoUrl 的关联数据<br/>
     *  4.3、添加新的 Media 与 VideoUrl 的关联数据<br/>
     *  4.4、更改 Media 转码状态 成功<br/>
     * 5、删除所有格式的 MtsService<br/>
     * 
     * @param string|Media $media       媒体资源(媒体ID或模型)
     * @param bool $force               是否强制添加（【正在转码中】和【已完成转码】的资源在没有设置force情况不再触发转码操作）
     * @author wskeee
     */
    public static function addVideoTranscode($media, $force = false) {
        if (!($media instanceof Media)) {
            $media = Media::findOne(['id' => $media, 'del_status' => 0]);
        }
        if (!$media) {
            throw new NotFoundHttpException('找不到对应资源！');
        }
        if($media->mediaType->sign != MediaType::SIGN_VIDEO){
            return; //非视频其它素材暂不支付转码
        }
        if ($media->is_link) {
            self::addLinkTrancode($media);
            return; //外联视频无法转码
        }
 
        //检查是否已经转码或者在转码中
        if ($force || $media->mts_status != Media::MTS_STATUS_YES) {
            //添加源视频格式
            $source_file = self::addMediaSource($media);
            //媒体详情
            $media_detail = MediaDetail::findOne(['media_id' => $media->id]);
            //水印配置
            $water_mark_options = Watermark::findAllForMts(['id' => explode(',', $media_detail->mts_watermark_ids), 'is_del' => 0]);
            //用户自定数据，转码后用于关联数据
            $user_data = [
                'is_redirect'    => 1,                                                          //设置重定向
                'redirect_url'   => Url::to('/external/aliyun-mts/task-complete', true),        //重定向地址
                'media_id'       => $media->id,
                'created_by'     => $source_file->created_by,
            ];
            
            //获取已完成转码文件等级
            $hasDoneLevels = []; //AliyunMtsService::getFinishLevel($media->id);
            if (count($hasDoneLevels) >= 4) {
                //4种格式都已完成
                self::integrateVideoTrancode($media->id, $force);
                return;
            }
            
            /**
             * 执行转码操作
             * 提交后等待转码完成回调 AliyunMtsController::actionTaskComplete()
             */
            $file_md5 = pathinfo($source_file->oss_key,PATHINFO_FILENAME);
            $transcode_save_path = Yii::$app->params['aliyun']['mts']['transcode_save_path'];
            $result = Aliyun::getMts()->addTranscode($source_file->oss_key, "{$transcode_save_path}{$file_md5}.mp4", $water_mark_options, $hasDoneLevels, $user_data);
            
            if ($result['success']) {
                //修改视频为转码中状态
                $media->mts_status = Media::MTS_STATUS_DOING;
                $tran = Yii::$app->db->beginTransaction();
                try {
                    //清旧任务记录
                    AliyunMtsService::updateAll(['is_del' => 1], ['media_id' => $media->id]);
                    //批量添加记录
                    AliyunMtsService::batchInsertServiceForMts($media->id, $result['response']);
                    $tran->commit();
                } catch (\Exception $ex) {
                    $tran->rollBack();
                    $rows = [];
                    $JobResult = $result['response']->JobResultList->JobResult;
                    $errorMessages = '';
                    foreach ($JobResult as $JobResult) {
                        if($JobResult->Success){
                            $rows [] = $JobResult->Job->JobId;             //任务ID;
                        }else{
                            $errorMessages.="$JobResult->Message\n";
                        }
                    }
                    //取消转码任务
                    if(count($rows)>0){
                        Aliyun::getMts()->cancelJob($rows);
                    }
                    
                    throw new \Exception('转码失败！'.$errorMessages);
                }
            } else {
                $media->mts_status = Media::MTS_STATUS_FAIL;
            }
            $media->save(false, ['mts_status']);
        }
    }

    /**
     * 添加媒体源始格式
     * @param Media $media
     * @return VideoUrl 源格式
     */
    private static function addMediaSource($media) {
        if ($media->is_link) {
            return self::addLinkMediaSource($media);
        }
        //找到源上传文件
        $file = Uploadfile::findOne(['id' => $media->file_id]);
        if (!$file) {
            throw new NotFoundHttpException("添加媒体源始格式失败，找不到媒体文件！");
        }
        $file_data = $file->toProcessedArray();
        //更新旧数据
        $source_video_url = VideoUrl::findOne(['media_id' => $media->id, 'is_original' => 1]);
        if (!$source_video_url) {
            $source_video_url = new VideoUrl();
        }
        $source_video_url->setAttributes([
            'media_id' => $media->id,
            'name' => VideoUrl::$videoLevelName[0],
            'url' => $file_data['url'],
            'oss_key' => $file_data['oss_key'],
            'size' => $file_data['size'],
            'level' => 0,
            'width' => $file_data['metadata']['width'],
            'height' => $file_data['metadata']['height'],
            'duration' => $file_data['metadata']['duration'],
            'bitrate' => $file_data['metadata']['bitrate'],
            'is_original' => 1,
            'is_del' => 0,
            'created_by' => $media->created_by,
        ]);
        if ($source_video_url->validate() && $source_video_url->save()) {
            return $source_video_url;
        } else {
            throw new \Exception("添加媒体源始格式失败：" . implode(',', $source_video_url->getErrorSummary(true)));
        }
    }

    /**
     * 添加外链媒体源始格式
     * @param Media $media
     */
    private static function addLinkMediaSource($media) {
        if (!$media->is_link) {
            return self::addMediaSource($media);
        }
        $file = EefileUtils::getVideoData($media->url);
        $metadata = json_decode($file['metadata'],true);

        //更新旧数据
        $source_video_url = VideoUrl::findOne(['media_id' => $media->id, 'is_original' => 1]);
        if (!$source_video_url) {
            $source_video_url = new VideoUrl();
        }
        $source_video_url->setAttributes([
            'media_id' => $media->id,
            'name' => VideoUrl::$videoLevelName[0],
            'url' => $file['oss_key'],
            'level' => 0,
            'size' => $file['size'],
            'width' => $metadata['width'],
            'height' => $metadata['height'],
            'duration' => $metadata['duration'],
            'bitrate' => $metadata['bitrate'],
            'is_original' => 1,
            'is_del' => 0,
            'created_by' => $media->created_by,
        ]);
        if ($source_video_url->validate() && $source_video_url->save()) {
            return $source_video_url;
        } else {
            throw new \Exception("添加媒体源始格式失败：" . implode(',', $source_video_url->getErrorSummary(true)));
        }
    }

    /**
     * 重试视频转码<br/>
     * 
     * @param string $media_id 
     * @author wskeee
     */
    public static function retryVideoTrancode($media_id) {
        $result = self::integrateVideoTrancode($media_id, true);
        if (!$result['success']) {
            //重新提交转码操作
            self::addVideoTranscode($media_id, true);
        }
    }

    /**
     * 整合视频转码 <br/>
     * 该满足 Aliyun 回调子任务时检查，同时满足 用户手动重试转码检查
     * 
     * 1、删除旧的 Media 与 VideoUrl 的关联数据<br/>
     * 2、添加新的 Media 与 VideoUrl 关联数据<br/>
     * 3、更改 Media 转码状态 成功<br/>
     * 4、删除所有格式的 MtsService<br/>
     * 
     * @param string $media_id    媒体转码请求ID
     * @param bool $force         强制执行，一般发生在 Aliyun 在回调失败时导致转码服务挂起，用户可通过手动重新转码调用
     * @author wskeee
     */
    public static function integrateVideoTrancode($media_id, $force = false) {
        //查出所有服务记录
        $mtsServices = AliyunMtsService::findAll(['media_id' => $media_id, 'is_del' => 0]);
        if ($mtsServices == null) {
            //没有转码记录,重新提交转码操作
            return ['success' => false, 'msg' => '没有转码记录'];
        }
        //所有服务都已完成
        if ($force || min(ArrayHelper::getColumn($mtsServices, 'is_finish')) == 1) {
            if (!$force) {
                //删除所有服务记录，避免重复检查
                AliyunMtsService::updateAll(['is_del' => 1], ['media_id' => $media_id]);
            }
            //查询任务详情结果
            $mtsResult = Aliyun::getMts()->queryJobList(ArrayHelper::getColumn($mtsServices, 'job_id'));
            //检查任务状态
            //Job.State：All表示所有状态，Submitted表示作业已提交，Transcoding表示转码中，TranscodeSuccess表示转码成功，TranscodeFail表示转码失败，TranscodeCancelled表示转码取消，默认是All
            $jobStates = ArrayHelper::getColumn($mtsResult['response']->JobList->Job, 'State');
            $jobStates = array_flip($jobStates);
            if (isset($jobStates['Submitted']) || isset($jobStates['Transcoding'])) {
                //任务正在进行中...中断操作
                return ['success' => true, 'msg' => '任务正在进行中...'];
            } else if (isset($jobStates['TranscodeFail']) || isset($jobStates['TranscodeCancelled'])) {
                //任务失败,重新提交转码操作
                return ['success' => false, 'msg' => '没有转码记录'];
            } else {
                //所有任务完成，继续执行下面操作
            }

            if ($mtsResult['success']) {
                $jobs = $mtsResult['response']->JobList->Job;
                //批量字段名
                $mediaTranscodeRowKeys = ['media_id', 'job_id', 'name', 'url', 'oss_key', 'level', 'size', 'width', 'height', 'duration', 'bitrate', 'created_by', 'created_at', 'updated_at'];
                //批量添加的 Uploadfile 数据
                $mediaTranscodeRows = [];

                $time = time();

                foreach ($jobs as $job) {
                    //任务ID
                    $jobId = $job->JobId;
                    //输出信息 Bucket、Location、Object
                    $outputFile = $job->Output->OutputFile;
                    //视频流信息 Profile、Width、Height、Index、Duration、Bitrate、
                    $mediaStream = $job->Output->Properties->Streams->VideoStreamList->VideoStream[0];
                    //整个视频信息 Duration、Size、Bitrate、
                    $format = $job->Output->Properties->Format;
                    //用户数据 level,media_id,created_by
                    $userData = json_decode($job->Output->UserData);

                    //添加对应 Uploadfile 数据
                    $mediaTranscodeRows [] = [
                        $media_id,                                      //视频ID
                        $jobId,                                         //id
                        VideoUrl::$videoLevelName[$userData->level],    //转码清晰度
                        Aliyun::absolutePath($outputFile->Object),      //外部访问路径
                        $outputFile->Object,                            //OSS文件名
                        $userData->level,                               //质量级别
                        $format->Size,                                  //视频总大小 单位：B
                        $mediaStream->Width,                            //宽
                        $mediaStream->Height,                           //高
                        $format->Duration,                              //视频时长
                        $format->Bitrate,                               //码率
                        
                        $userData->created_by,                          //创建人
                        $time,                                          //创建时间
                        $time                                           //更新时间
                    ];
                }

                //插入数据库
                $tran = Yii::$app->db->beginTransaction();
                try {
                    //删除旧关联(不包括源文件关联)
                    VideoUrl::updateAll(['is_del' => 1], ['media_id' => $media_id, 'is_del' => 0 , 'is_original' => 0]);
                    //插入 VideoUrl
                    Yii::$app->db->createCommand()->batchInsert(VideoUrl::tableName(), $mediaTranscodeRowKeys, $mediaTranscodeRows)->execute();
                    //更改 Media 转码状态 成功, Media 时长 ，【注意】并且改为发布状态
                    Yii::$app->db->createCommand()->update(Media::tableName(), ['mts_status' => Media::MTS_STATUS_YES, 'status' => Media::STATUS_PUBLISHED, 'duration' => $format->Duration], ['id' => $media_id])->execute();
                    if ($force) {
                        //如果为强制，即删除所有服务记录（前面未删除）
                        AliyunMtsService::updateAll(['is_del' => 1], ['media_id' => $media_id]);
                    }
                    $tran->commit();
                    return ['success' => true, 'msg' => '转码服务已完成'];
                } catch (Exception $ex) {
                    $tran->rollBack();
                    //更改 Media 转码状态为 失败
                    Yii::$app->db->createCommand()->update(Media::tableName(), ['mts_status' => Media::MTS_STATUS_FAIL], ['id' => $media_id])->execute();
                    Yii::error($ex->getMessage(), __FUNCTION__);
                    return ['success' => false, 'msg' => '转码服务失败：' . $ex->getMessage()];
                }
            }
        } else {
            return ['success' => true, 'msg' => '转码进行中...'];
        }
    }

    /**
     * 添加外链转码，该方法只作其它质量关联，没有做真正的转码
     * 1、找出视频的其它质量
     * 2、删除旧的关联
     * 3、添加新的关联
     * 
     * @param string|Media $media
     */
    private static function addLinkTrancode($media) {
        if (!($media instanceof Media)) {
            $media = Media::findOne(['id' => $media, 'del_status' => 0]);
        }
        if (!$media) {
            throw new NotFoundHttpException('找不到对应资源！');
        }
        if (!$media->is_link) {
            return; //非外联视频请使用AddTrancode
        }
        
        if($media->mts_status != Media::MTS_STATUS_YES){
            $tran = \Yii::$app->db->beginTransaction();
            //添加源视频格式
            self::addLinkMediaSource($media);
            try {
                /* 分析文件路径 */
                $media_path_info = pathinfo($media->url);
                //目录路径
                $media_path_basepath = $media_path_info['dirname'];
                $formats = ['LD','SD','HD','FD'];
                $rowKeys = ['media_id', 'name', 'url', 'level', 'size', 'width', 'height', 'duration', 'bitrate', 'created_by', 'created_at', 'updated_at'];
                $rows = [];
                $time = time();
                foreach($formats as $index => $format){
                    $file = EefileUtils::getVideoData("$media_path_basepath/$format/{$media_path_info['basename']}");
                    $metadata = json_decode($file['metadata'],true);
                    if(!$file)continue;
                    $rows []= [
                        $media->id,
                        VideoUrl::$videoLevelName[$index],
                        $file['oss_key'],
                        $metadata['level'],
                        $file['size'],
                        $metadata['width'],
                        $metadata['height'],
                        $metadata['duration'],
                        $metadata['bitrate'],
                        Yii::$app->user->id,
                        $time,
                        $time,
                    ];
                }                
                //删除旧关联(不包括源文件关联)
                VideoUrl::updateAll(['is_del' => 1], ['media_id' => $media->id, 'is_del' => 0, 'is_original' => 0]);
                //插入新的VideoUrl关联
                Yii::$app->db->createCommand()->batchInsert(VideoUrl::tableName(), $rowKeys, $rows)->execute();
                //更改 Media 转码状态
                Yii::$app->db->createCommand()->update(Media::tableName(), ['mts_status' => Media::MTS_STATUS_YES, 'status' => Media::STATUS_PUBLISHED], ['id' => $media->id])->execute();
                $tran->commit();
            } catch (\Exception $ex) {
                $tran->rollBack();
                //更改 Media 转码状态
                Yii::$app->db->createCommand()->update(Media::tableName(), ['mts_status' => Media::MTS_STATUS_FAIL], ['id' => $media->id])->execute();
                Yii::error("外链转码失败：{$ex->getMessage()}", __FUNCTION__);
                Yii::$app->session->setFlash('error',"外链转码失败：{$ex->getMessage()}");
            }
        }
    }

    /**
     * 从oss上删除视频资源(包括其转码视频)
     * @param string|Media $media   视频资源
     */
    public static function removeMediaFromOSS($media) {
        return "";//功能未完成
        
        if (!($media instanceof Media)) {
            $media = Media::findOne(['id' => $media, 'del_status' => 0]);
        }
        if (!$media) {
            throw new NotFoundHttpException('找不到对应资源！');
        }
        if ($media->is_link) {
            return; //外联视频删除
        }

        //$oss_keys = VideoTranscode::find()->select(['oss_key'])->where(['video_id' => $media->id])->column();
        //$oss_keys []= $media->file->oss_key;

        //删除阿里云文件
        if ($oss_keys && count($oss_keys) > 0) {
            //var_dump(Aliyun::getOss()->deleteObject($oss_keys[1]));
            foreach ($oss_keys as &$oss_key) {
                $oss_key = urlencode($oss_key);
            }
            Aliyun::getOss()->deleteObjects($oss_keys);
        }
        //设置逻辑删除
    }

    /**
     * 添加视频截图
     * 
     * @param string|Media $media       视频ID｜视频模型
     * @param int $start_time           截图时间
     * @author wskeee
     */
    public static function addVideoSnapshot($media, $start_time = 3000) {
        
        throw new \Exception('截图功能暂不开放使用，截图在上传文件时已完成！无需重新截图');
        
        if (!($media instanceof Media)) {
            $media = Media::findOne(['id' => $media, 'del_status' => 0]);
        }
        if (!$media) {
            throw new NotFoundHttpException('找不到对应资源！');
        }
        if ($media->is_link) {
            return; //外联视频无法截图
        }
        //查询源视频文件
        $file = VideoUrl::findOne(['media_id' => $media->id, 'is_del' => 0, 'is_original' => 1]);
        if (!$file) {
            throw new NotFoundHttpException('找不到原始视频！');
        }
        //提交截图任务(异步)
        $screenshot_save_path = Yii::$app->params['aliyun']['mts']['screenshot_save_path'];
        $result = Aliyun::getMts()->submitSnapshotJob($file->oss_key, "{$screenshot_save_path}{$media->id}.jpg");
        if ($result['success']) {
            try {
                //获取截图路径
                $snapshot_paths = $result['snapshot_paths'];
                //更新Video和源文件图片路径
                $media->cover_url = Aliyun::absolutePath($snapshot_paths[0]);
                $media->save(false, ['cover_url']);
            } catch (Exception $ex) {
                Yii::error("Vid= {$media->id},截图失败：{$ex->getMessage()}");
            }
        }
    }

}
