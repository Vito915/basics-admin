<?php

use common\modules\webuploader\assets\DemoAsset;
use common\modules\webuploader\models\Uploadfile;
use common\widgets\webuploader\Webuploader;
use yii\web\View;

/* @var $this View */

DemoAsset::register($this);
?>

<div class="webuploader-demo-index">
    <div class="container demo">
        <div class="section">
            <div class="header"><h2>文件上传组件 Webupload Widget</h2></div>
            <div class="des">Webuploader上传组件
                功能：单文件上传、多文件上传、缩略图显示、预览、断点续传</div>
        </div>

        <div class="section">
            <div class="header"><h3>1、快速使用</h3></div>
            <div class="des">无需要任何参数直接创建使用</div>
            <div class="example">
                <?=
                Webuploader::widget(['name' => 'files']);
                ?>
            </div>
            <div>
                <pre>
<code class="php">
&lt;div class=&quot;example&quot;&gt;
  &lt;?=
  Webuploader::widget(['name' =&gt; 'files']);
  ?&gt;
&lt;/div&gt;
</code>
                </pre>
            </div>
        </div>
        
        <div class="section">
            <div class="header"><h3>2、插件参数设置</h3></div>
            <div class="des"></div>
            <div class="example">
                <?=
                Webuploader::widget([
                    'name' => 'files',
                    'pluginOptions' => [
                        'fileNumLimit' => 3,
                        'auto' => false,
                        'accept' => [
                            'title' => 'Material',
                            'extensions' => 'mp4,mp3,gif,jpg,jpeg,bmp,png,doc,docx,txt,xls,xlsx,ppt,pptx',
                            'mimeTypes' => 'video/mp4,audio/mp3,image/*,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx',
                        ],
                        // 文件接收服务端。
                        'server' => '/webuploader/default/upload',
                        //检查文件是否存在
                        'checkFile' => '/webuploader/default/check-file',
                        //分片合并
                        'mergeChunks' => '/webuploader/default/merge-chunks',
                    ]
                ]);
                ?>
            </div>
            <div>
                <pre>
<code class="php">
&lt;div class=&quot;example&quot;&gt;
	&lt;?=
	Webuploader::widget([
		'name' =&gt; 'files',
		'pluginOptions' =&gt; [
			//设置最大选择文件数
			'fileNumLimit' =&gt; 3,		
			//设置是否自动开始上传
			'auto' =&gt; false,
                        //设置分页，每页显示多少项
			'pageSize' =&gt; 10,
			//设置允许选择的文件类型
			'accept' =&gt; [
				'title' =&gt; 'Material',
				'extensions' =&gt; 'mp4,mp3,gif,jpg,jpeg,bmp,png,doc,docx,txt,xls,xlsx,ppt,pptx',
				'mimeTypes' =&gt; 'video/mp4,audio/mp3,image/*,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx',
			],
			// 文件接收服务端。
			'server' =&gt; '/webuploader/default/upload',
			//检查文件是否存在
			'checkFile' =&gt; '/webuploader/default/check-file',
			//分片合并
			'mergeChunks' =&gt; '/webuploader/default/merge-chunks',
		]
	]);
	?&gt;
&lt;/div&gt;
</code>
                </pre>
            </div>
        </div>
        
        <div class="section">
            <div class="header"><h3>3、侦听事件</h3></div>
            <div class="des">侦听上传组件事件，可实现初始化、手动控制进度</div>
            <div class="example">
                <?=
                Webuploader::widget([
                    'name' => 'files',
                    'pluginOptions' => [
                        'auto' => true,
                    ],
                    'pluginEvents' => [
                        'ready' => 'function(uploader){$("#section3-info").append("初始化完成，")}',
                        'uploadFinished' => 'function(evt,file){$("#section3-info").append("上传完成")}',
                ]]);
                ?>
                <h4>状态：</h4>
                <div id="section3-info"></div>
            </div>
            <div>
                <pre>
<code class="php">
&lt;div class=&quot;example&quot;&gt;
	&lt;?=
	Webuploader::widget([
		'name' =&gt; 'files',
		'pluginOptions' =&gt; [
			'auto' =&gt; false,
		],
		'pluginEvents' =&gt; [
			'ready' =&gt; 'function(uploader){consloe.log(&quot;初始化完成&quot;)}',				
			'uploadFinished' =&gt; 'function(evt,file){console.log(&quot;上传完成&quot;)}',
	]]);
	?&gt;
&lt;/div&gt;

支持以下事件：
'ready' =&gt; 'function(uploader){}',                                      //插件初始化完成
'fileQueued' =&gt; 'function(evt,file,hasDone){}',                         //文件添加入队列
&quot;fileDequeued&quot; =&gt; 'function(evt,file){}',                               //文件从队列中删除
&quot;uploadProgress&quot; =&gt; 'function(evt,file,percentage){}',                  //文件上传进度
&quot;uploadSuccess&quot; =&gt; 'function(evt,file,response){}',                     //文件上传成功
&quot;uploadError&quot; =&gt; 'function(evt,file,reason,isExist){}',                 //文件上传错误
&quot;uploadComplete&quot; =&gt; 'function(evt,dbFile,file){}',                      //文件上传完成(成功失败都会触发)
&quot;uploadFinished&quot; =&gt; 'function(evt,file){}',                             //所有文件上传完成
&quot;fileMd5Progress&quot; =&gt; 'function(evt,file,percentage){}',                 //文件md5计算进度
&quot;fileMd5Complete&quot; =&gt; 'function(evt,file,md5Id){}',                      //文件md5计算完成    
</code>
                </pre>
            </div>
        </div>
        
        <div class="section">
            <div class="header"><h3>4、手动控制流程</h3></div>
            <div class="des">取消自动上传，$('#file-uploader').data('uploader') 拿到uploader，再通过upload()、retry()方法控制上传</div>
            <div class="example">
                <?=
                Webuploader::widget([
                    'id' => 'file-uploader',
                    'name' => 'files',
                    'pluginOptions' => [
                        'auto' => false,
                    ],
                ]);
                ?>
                <a class="btn btn-default" onclick="getUploader()">上传</a>
            </div>
            <script>
                function getUploader(){
                    var uploader = $('#file-uploader').data('uploader');
                    uploader.upload();          //开始上传
                    //uploader.retry();           //重试所有
                }
            </script>
            <div>
                <pre>
<code class="php">
&lt;div class=&quot;example&quot;&gt;
	&lt;?=
	Webuploader::widget([
		'id' =&gt; 'file-uploader',		//通过ID获取上传对象
		'name' =&gt; 'files',
		'pluginOptions' =&gt; [
			'auto' =&gt; false,
		],
	]);
	?&gt;
&lt;/div&gt;
&lt;a class=&quot;btn btn-default&quot; onclick=&quot;getUploader()&quot;&gt;获取Uploader对象&lt;/a&gt;
&lt;script&gt;
    function getUploader(){
        var uploader = $('#file-uploader').data('uploader');
        uploader.upload();          //开始上传
        //uploader.retry();           //重试所有
    }
&lt;/script&gt;
</code>
                </pre>
            </div>
        </div>
        
        <div class="section">
            <div class="header"><h3>5、恢复已上传文件</h3></div>
            <div class="des">显示以前已经上传的文件 设置 data(array) 属性 每个项必须包括(id,name,ext,url,thumb_ur,size)</div>
            <div class="example">
                <?php
                $files = Uploadfile::find()->limit(10)->all();
                $data = [];
                /* @var $file Uploadfile */
                foreach ($files as $file) {
                    $data[] = $file->toProcessedArray();
                }
                echo Webuploader::widget([
                    'name' => 'files',
                    'data' => $data,
                ]);
                ?>
            </div>
            <div>
                <pre>
<code class="php">
&lt;div class=&quot;example&quot;&gt;
	&lt;?php
	$files = Uploadfile::find()-&gt;limit(10)-&gt;all();
	$data = [];
	/* @var $file Uploadfile */
	foreach ($files as $file) {
		$data[] = $file-&gt;toProcessedArray();
	}
	echo Webuploader::widget([
		'name' =&gt; 'files',
		'data' =&gt; $data,
	]);
	?&gt;
&lt;/div&gt;
</code>
                </pre>
            </div>
        </div>
    </div>
    <script type='text/javascript'>

    </script>
</div>
<?php 
    $this->registerJs('hljs.initHighlightingOnLoad();');
?>