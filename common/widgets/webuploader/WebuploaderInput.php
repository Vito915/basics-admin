<?php

namespace common\widgets\webuploader;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Webuploader上传组件
 * 功能：单文件上传、多文件上传、缩略图显示、预览、断点续传
 *
 * @author Administrator
 */
class WebuploaderInput extends InputWidget{
    /* 
     * Tile块方式显示，可预览缩略图 
     * 使用建议：
     *  适合少量文件上传
     *  适合头像上传
     */
    const TYPE_TILE = 'TileView';
    /**
     * 表格方式显示，不具备预览缩略图
     * 使用建议
     *  适合大数量文件上传
     */
    const TYPE_TABLE_LIST = 'FileListView';
    
    /**
     * 上传组件参数
     **/
    public $pluginOptions = [
        'type' => self::TYPE_TABLE_LIST,                                        //视图类型 TileView OR FileListView
        'fileNumLimit' => 100,                                                  //最大文件数量
        'targetAttribute' => 'id',                                              //作为formData使用时最终上传到服务器的属性字段 ,比如头像获取时，可使用url，直接获取上传后的文件路径
        'formData' => [],                                                       //在上传过程中一并带到服务器的参数
        //自动上传
        'auto' =>  true,                                                        //默认自动上传，设置为false 需要手动调用 upload()、retry() 等方法上传
        //指定接受哪些类型的文件
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
    ];
    /**
     * 组件事件
     * @var type 
     */
    public $pluginEvents = [
        'ready' => 'function(uploader){}',                                      //插件初始化完成
        'fileQueued' => 'function(evt,file,hasDone){}',                         //文件添加入队列
        "fileDequeued" => 'function(evt,file){}',                               //文件从队列中删除
        "uploadProgress" => 'function(evt,file,percentage){}',                  //文件上传进度
        "uploadSuccess" => 'function(evt,file,response){}',                     //文件上传成功
        "uploadError" => 'function(evt,file,reason,isExist){}',                 //文件上传错误
        "uploadComplete" => 'function(evt,dbFile,file){}',                      //文件上传完成(成功失败都会触发)
        "uploadFinished" => 'function(evt,file){}',                             //所有文件上传完成
        "fileMd5Progress" => 'function(evt,file,percentage){}',                 //文件md5计算进度
        "fileMd5Complete" => 'function(evt,file,md5Id){}',                      //文件md5计算完成    
    ];

    public function __construct($config = array()) {
        $config['pluginOptions'] = array_merge($this->pluginOptions, isset($config['pluginOptions']) ? $config['pluginOptions'] : []);
        $config['pluginEvents'] = array_merge($this->pluginEvents, isset($config['pluginEvents']) ? $config['pluginEvents'] : []);
        if(!isset($config['id'])){
            $this->setId('Webuploader_'. rand(100000, 999999));
        }
        parent::__construct($config);
    }

    public function init(){
        parent::init();
        
        /**
         * 合并数据
         */
        $this->pluginOptions['formData'][Yii::$app->request->csrfParam] = Yii::$app->request->csrfToken;                        //添加csrf
        $this->pluginOptions['name'] = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;    //formData 属性名，多个文件请使用[]数组命名
        $this->pluginOptions['container'] = "#{$this->id}";                                                                     //组件根目录
        
        $this->registerAssets();
    }
    
    public function run(){
        return Html::tag('div', '', ['id' => $this->id]);
    }
    
    public function registerAssets() {
        $view = $this->getView();
        //获取flash上传组件路径
        $sourcePath = $view->assetManager->getPublishedUrl(WebUploaderAsset::register($view)->sourcePath);
        
        //设置组件配置
        $this->pluginOptions['sourcePath'] = $sourcePath;
        
        $config = Json::encode($this->pluginOptions);
        /* 准备插件事件 */
        $pluginEvents = "";
        foreach($this->pluginEvents as $key => $value){
            if($key != 'ready'){
                $pluginEvents .= "$(uploader).on('$key',$value)\n";
            }
        }
        /* 准备已存在数据 */
        $datas = json_encode($this->convertDbfiles());
        
        $js = <<< JS
            Wskeee.require(['euploader'],function(){
                var config = $config;
                var uploader = new euploader.Uploader(config,euploader[config['type']]);
                var ready = {$this->pluginEvents['ready']};
                var datas = $datas;
                
                //保存loader与div关系
                $("#$this->id").data('uploader',uploader);
                //添加事件
                $pluginEvents;
                //添加已经存在文件
                uploader.addCompleteFiles(datas);
                //调用ready初始化完成
                ready(uploader);
            });
JS;
        $view->registerJs($js, View::POS_READY);
    }
    
    /**
     * 转换数据文件
     * @return string json
     */
    protected function convertDbfiles(){
        //override
        return [];
    }
}
