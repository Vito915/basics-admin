<?php
return [
    /* ffmpeg配置 */
    'ffmpeg' => [
        'ffmpeg.binaries' => '/usr/bin/ffmpeg',
        'ffprobe.binaries' => '/usr/bin/ffprobe',
    ],
     /* 阿里云OSS配置 */
    'aliyun' => [
        'accessKeyId' => 'LTAIM0fcBM6L6mTa',
        'accessKeySecret' => '2fSyGRwesxyP4X2flUF35n7brgxlEf',
        'oss' => [
            'bucket-input' => 'studying8',
            'bucket-output' => 'studying8',
            'host-input' => 'studying8.oss-cn-shenzhen.aliyuncs.com',                   
            'host-output' => 'file.studying8.com',                  
            'host-input-internal' => 'studying8.oss-cn-shenzhen-internal.aliyuncs.com',
            'host-output-internal' => 'studying8.oss-cn-shenzhen-internal.aliyuncs.com',
            'endPoint' => 'oss-cn-shenzhen.aliyuncs.com',
            'endPoint-internal' => 'oss-cn-shenzhen-internal.aliyuncs.com',
        ],
        'mts' => [
            'region_id' => 'cn-shenzhen',                               //区域
            'pipeline_id' => 'd51a05c98fca4984923e7fb6f5536a45',        //管道ID
            'pipeline_name' => 'new-pipeline',                          //管道名称
            'oss_location' => 'oss-cn-shenzhen',                        //作业输入，华南1
            'template_id_ld' => '318df02dd6f64ab7bc9fb5f2d518e75c',     //流畅模板ID
            'template_id_sd' => 'c3d3535de33d47e7b7fa7f0dfad6058b',     //标清模板ID
            'template_id_hd' => '7a4da48e63b545eea6bf4299aecaf247',     //高清模板ID
            'template_id_fd' => '9cd7b352feb3426aa1f8f9fc0dcd6baa',     //超畅模板ID
            'water_mark_template_id' => '15b2d6094e8448c493cd113a90e330e3',     //水印模板ID 默认右上
            'topic_name' => 'studying8-transcode',                      //消息通道名
            'transcode_save_path' => 'mediacloud/transcode/',           //转码后保存路径
            'screenshot_save_path' => 'mediacloud/thumb/',              //截图后保存路径
        ]
    ],
];
