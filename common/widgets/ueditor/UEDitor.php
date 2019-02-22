<?php

namespace common\widgets\ueditor;

use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * UEDitor 富文本编辑器
 * 
 * 【注意】
 * 使用 boostrap的模态框，并且以动态load方式加载该组件页面时，
 * 必须在模型框关闭时对组件进行销毁，否则组件无法再次初始化
 *  如：
 *  $('#my-modal').on('hide.bs.modal', function (e) {
        //清除框态框侦听事件，避免重复侦听
        $('#my-modal').off('hide.bs.modal');
        //销毁组件，通过ID，获取到组件本体，再调用 destory 方法
        $('#des-editor').data('editor').destroy();
    });
 *
 * @author Administrator
 */
class UEDitor extends InputWidget {

    /**
     * 插件选项
     * @var array 
     */
    public $pluginOptions = [
        'zIndex' => 999,                       //editor-container z-index 值，1050 为modal的z-index，低于1050 在模态框无法弹出菜单
        'initialFrameHeight' => 200,            //初始高度
        'maximumWords' => 100000,               //最大输入字符数
        'toolbars' => [                         //工具条配置 更多请查看 http://fex.baidu.com/ueditor/#start-toolbar
            [
                'fullscreen', 'source', '|',
                'paragraph', 'fontfamily', 'fontsize', '|',
                'forecolor', 'backcolor', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch', '|',
                'justifyleft', 'justifyright', 'justifycenter', 'justifyjustify', '|',
                'insertorderedlist', 'insertunorderedlist', 'simpleupload', 'horizontal', '|',
                'selectall', 'cleardoc',
                'undo', 'redo',
            ]
        ]
    ];
    /**
     * 插件事件
     * @var array 
     */
    public $pluginEvents = [
        'ready' => 'function(editor){}',                                              //编辑器准备就绪后会触发该事件，如：function(editor){editor.execCommand( 'focus' )); 编辑器家在完成后，让编辑器拿到焦点
        'destroy' => 'function(editor){}',                                            //执行destroy方法,会触发该事件 UE.Editor:destroy()
        'reset' => 'function(editor){}',                                              //执行reset方法,会触发该事件 UE.Editor:reset()
        'focus' => 'function(editor){}',                                              //执行focus方法,会触发该事件 UE.Editor:focus(Boolean)
        'beforeGetContent' => 'function(editor){}',                                   //在getContent方法执行之前会触发该事件 UE.Editor:getContent()
        'beforeSetContent' => 'function(editor){}',                                   //在setContent方法执行之前会触发该事件 UE.Editor:setContent(String)
        'afterSetContent' => 'function(editor){}',                                    //在setContent方法执行之后会触发该事件 UE.Editor:setContent(String)
        'selectionchange' => 'function(editor){}',                                    //每当编辑器内部选区发生改变时，将触发该事件
        'contentChange' => 'function(editor){}',                                      //编辑器内容发生改变时会触发该事件
    ];
    
    /**
     * input样式
     **/
    public $options = [
        'style' => ['width' => '100%']
    ];

    public function __construct($config = array()) {
        $config['pluginOptions'] = array_merge($this->pluginOptions, isset($config['pluginOptions']) ? $config['pluginOptions'] : []);
        $config['pluginEvents'] = array_merge($this->pluginEvents, isset($config['pluginEvents']) ? $config['pluginEvents'] : []);
        $config['options'] = array_merge(['id' => 'UEDitor_'. rand(100000, 999999)],$this->options, isset($config['options']) ? $config['options'] : []);
        if(!isset($config['id'])){
            $this->setId($config['options']['id']);
        }else{
            $config['options']['id'] = $config['id'];
        }
        parent::__construct($config);
    }

    public function init() {
        parent::init();
        $this->value = $this->hasModel() ? BaseHtml::getAttributeValue($this->model, $this->attribute) : $this->value;
        $this->name = $this->hasModel() ? BaseHtml::getInputName($this->model, $this->attribute) : $this->name;
        $this->registerAssets();
    }

    public function run() {
        //获取值传入插件
        return BaseHtml::textarea($this->name, Html::decode($this->value), $this->options);
    }

    public function registerAssets() {
        $view = $this->getView();
        //获取flash上传组件路径
        $sourcePath = $view->assetManager->getPublishedUrl(UeditorAsset::register($view)->sourcePath);

        //设置组件配置
        $this->pluginOptions['sourcePath'] = $sourcePath;

        $config = Json::encode($this->pluginOptions);
        
        /* 准备插件事件 */
        $pluginEvents = "";
        foreach($this->pluginEvents as $key => $value){
            $pluginEvents .= "editor.addListener( '$key',function(){($value).apply(editor,[editor])});\n";
        }
        $id = $this->id;
        $js = <<< JS
        (function(){
            var editor = new UE.getEditor('$id',$config);
            //保存editor与div关系
            $("#$id").data('editor',editor);
            //添加事件
            $pluginEvents;
            //侦听内容改变事件
            editor.addListener( 'contentChange', function( ) {
                $("#$id").val(editor.getContent());
            });
        })();
JS;
        $view->registerJs($js);
    }

}
