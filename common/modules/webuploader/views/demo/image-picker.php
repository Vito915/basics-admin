<?php

use common\models\User;
use common\modules\webuploader\assets\DemoAsset;
use common\widgets\webuploader\ImagePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */

DemoAsset::register($this);
?>

<div class="webuploader-demo-index">
    <div class="container demo">
        <div class="section">
            <div class="header"><h2>图片获取器 ImagePicker</h2></div>
            <div class="des">图片获取器 ImagePicker
                功能：单个头像获取、多个图片获取，比较适合单张图片获取，可结合Model使用</div>
        </div>

        <div class="section">
            <div class="header"><h3>1、快速使用</h3></div>
            <div class="des">无需要任何参数直接创建使用</div>
            <div class="example">
                <?=
                ImagePicker::widget(['name' => 'avatar']);
                ?>
            </div>
            <div>
                <pre>
<code class="php">
&lt;div class=&quot;example&quot;&gt;
	&lt;?=
	ImagePicker::widget(['name' =&gt; 'avatar']);
	?&gt;
&lt;/div&gt;
</code>
                </pre>
            </div>
        </div>

        <div class="section">
            <div class="header"><h3>2、form使用（推荐使用）</h3></div>
            <div class="des">和其它Widget组件使用方法一样</div>
            <div class="example">
                <?php $model = new User(['avatar' => 'http://file.studying8.com/mediacloud/files/1d973c9b3e63da94ce0ddb5f9e969e6d.jpg']); ?>
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'avatar')->widget(ImagePicker::class); ?>
                <div class="form-group">
                    <?= Html::submitButton('提交',['class' => 'btn btn-primary','onclick' => '']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div>
                <pre>
<code class="php">
&lt;div class=&quot;example&quot;&gt;
	&lt;?php $model = new User(['avatar' =&gt; 'http://file.studying8.com/mediacloud/files/1d973c9b3e63da94ce0ddb5f9e969e6d.jpg']); ?&gt;
	&lt;?php $form = ActiveForm::begin(); ?&gt;
        
	&lt;?= $form-&gt;field($model, 'avatar')-&gt;widget(ImagePicker::class); ?&gt;
        
	&lt;div class=&quot;form-group&quot;&gt;
		&lt;?= Html::submitButton('提交', ['class' =&gt; 'btn btn-primary']) ?&gt;
	&lt;/div&gt;
	&lt;?php ActiveForm::end(); ?&gt;
&lt;/div&gt;
</code>
                </pre>
            </div>
        </div>
        
        <div class="section">
            <div class="header"><h3>3、插件参数设置</h3></div>
            <div class="des"></div>
            <div class="example">
                <?=
                ImagePicker::widget([
                    'name' => 'imgs',
                    'pluginOptions' => [
                        'fileNumLimit' => 3,
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
	ImagePicker::widget([
		'name' =&gt; 'files',
		'pluginOptions' =&gt; [
			//设置最大选择文件数
			'fileNumLimit' =&gt; 3,	
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
            <div class="header"><h3>4、侦听事件</h3></div>
            <div class="des">侦听上传组件事件，可实现初始化、手动控制进度</div>
            <div class="example">
                <?=
                ImagePicker::widget([
                    'name' => 'avatar',
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
	ImagePicker::widget([
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
            <div class="header"><h3>5、手动控制流程</h3></div>
            <div class="des">取消自动上传，$('#file-uploader').data('uploader') 拿到uploader，再通过upload()、retry()方法控制上传</div>
            <div class="example">
                <?=
                ImagePicker::widget([
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
	ImagePicker::widget([
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
            <div class="header"><h3>6、恢复已上传文件</h3></div>
            <div class="des">显示以前已经上传的文件 设置 value(string|array) 属性，多个url，使用，分隔</div>
            <div class="example">
                <?php
                //单图像
                //$model = new User(['avatar' => 'http://file.studying8.com/mediacloud/files/1d973c9b3e63da94ce0ddb5f9e969e6d.jpg']);
                //数组
                $urls = [
                    'http://file.studying8.com/mediacloud/files/1d973c9b3e63da94ce0ddb5f9e969e6d.jpg',
                    'http://file.studying8.com/mediacloud/files/b40db372ba8afea656cf79033c6b701d.jpg',
                    'http://file.studying8.com/mediacloud/files/66daa0f28284b9bacfca130131d3aadb.jpg',
                    'http://file.studying8.com/mediacloud/files/888db6efa9bcd669f194938b23f2c9a0.jpg',
                    ];
                //字符串，使用，分隔多张图片
                //$urls = 'http://file.studying8.com/mediacloud/files/1d973c9b3e63da94ce0ddb5f9e969e6d.jpg,url2,url3,http://file.studying8.com/mediacloud/files/b40db372ba8afea656cf79033c6b701d.jpg'
                
                $value = [];
                
                echo ImagePicker::widget([
                    'name' => 'files',
                    'value' => $urls,
                ]);
                ?>
            </div>
            <div>
                <pre>
<code class="php">
&lt;div class=&quot;example&quot;&gt;
	&lt;?php
	//单图像
	//$model = new User(['avatar' =&gt; 'http://file.studying8.com/mediacloud/files/1d973c9b3e63da94ce0ddb5f9e969e6d.jpg']);
	//数组
	$urls = [
		'http://file.studying8.com/mediacloud/files/1d973c9b3e63da94ce0ddb5f9e969e6d.jpg',
		'http://file.studying8.com/mediacloud/files/b40db372ba8afea656cf79033c6b701d.jpg',
		'http://file.studying8.com/mediacloud/files/66daa0f28284b9bacfca130131d3aadb.jpg',
		'http://file.studying8.com/mediacloud/files/888db6efa9bcd669f194938b23f2c9a0.jpg',
		];
	//字符串，使用，分隔多张图片
	//$urls = 'http://file.studying8.com/mediacloud/files/1d973c9b3e63da94ce0ddb5f9e969e6d.jpg,url2,url3,http://file.studying8.com/mediacloud/files/b40db372ba8afea656cf79033c6b701d.jpg'
	
	$value = [];
	
	echo ImagePicker::widget([
		'name' =&gt; 'files',
		'value' =&gt; $urls,
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