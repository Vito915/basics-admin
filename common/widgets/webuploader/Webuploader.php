<?php

namespace common\widgets\webuploader;

/**
 * 列表试上传组件
 *
 * @author Administrator
 */
class Webuploader extends WebuploaderInput{
    /**
     * 已完成上传的文件集合
     * @var array[[id,ext,url,thumb_url,size,name],[],[]] 
     */
    public $data = null;
    
    public function init() {
        $this->pluginOptions['type'] = self::TYPE_TABLE_LIST;
        $this->pluginOptions['targetAttribute'] = 'id';
        
        parent::init();
    }
    
    /**
     * 转换数据文件
     * @return string json
     */
    protected function convertDbfiles(){
        if(empty($this->data)){
            return [];
        }else{
            if(is_array($this->data)){
                return $this->data;
            }else{
                throw new Exception('value 必须为数组，并且包括id,name,size,url,thumb_url属性!');
            }
        }
    }
}
