<?php

namespace common\widgets\webuploader;

use Exception;
use yii\helpers\BaseHtml;

/**
 * 图片获取器
 * 功能：上传头像，或者多长图片
 *
 * @author Administrator
 */
class ImagePicker extends WebuploaderInput{
    
    public function __construct($config = array()) {
        $this->pluginOptions['fileNumLimit'] = 1;
        $this->pluginOptions['accept'] = [
            'title' => '选择图片',
            'extensions' => 'gif,jpg,jpeg,bmp,png',
            'mimeTypes' => 'image/*',
        ];
        parent::__construct($config);
    }
    
    public function init(){
        $this->pluginOptions['type'] = self::TYPE_TILE;
        $this->pluginOptions['targetAttribute'] = 'url';
        
        parent::init();
    }
    
    /**
     * 由url转换数据文件
     * @return string json
     */
    protected function convertDbfiles(){
        $value = $this->hasModel() ? BaseHtml::getAttributeValue($this->model, $this->attribute) : $this->value;
        if(empty($value)){
            return [];
        }else{
            $datas = [];
            $urls = [];
            
            if(is_string($value)){
                $urls = explode(',', $value);
            }else if(is_array($value)){
                $urls = $value;
            }else{
                throw new Exception('value 格式无法识别！');
            }
            foreach($urls as $url){
                $datas []= $this->createDbfile($url);
            }
            return $datas;
        }
    }
    
    /**
     * 通过 url 创建object
     * @param string $url
     */
    protected function createDbfile($url){
        return [
            'id' => "image-picker-". rand(10000, 99999),
            'thumb_url' => $url,
            'url' => $url,
            'ext' => pathinfo($url,PATHINFO_EXTENSION),
            'size' => 0,
            'name' => '',
        ];
    }
}
