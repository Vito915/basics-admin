(function (win, $) {
    /**
     * 
     * @param {Object} config
     *  container string 容器id
     *  background string 图片 or 颜色
     *  watermark string 水印（默认是图片）
     * @returns {void}
     */
    var Watermark = function(config){
        /* 配置 */
        this.config = $.extend({
            container: '#preview-watermark',
            class: 'preview',
            watermark: '<img />',
        },config);
        
        /* 容器 */
        this.container = $(this.config['container']);
        /* 添加背景 */
        this.container.addClass(this.config['class']);
    }
    
    /**
     * 添加水印
     * @param {type} config        水印配置{refer_pos,url,width, height, dx, dy}
     * @param {string} water_id    水印ID
     * @returns {void}
     */
    Watermark.prototype.addWatermark = function (config, water_id) 
    {
        var water_id = water_id || 0,
            config = config || {},
            container = this.container,     //预览图
            watermark = $(this.config['watermark']);    //获取对应 watermark_dom
            
        //找到原先的 watermark_dom，如果没有新建
        if(container.find(watermark).length <= 0){
            watermark.attr({id: 'watermark' + water_id, class: 'watermark'}).appendTo(container);
        }
        
        //更新水印
        this.updateWatermark(config, water_id);
    }

    /**
     * 更新水印
     * @param {type} config          水印配置{refer_pos,url,width, height, dx, dy}    
     * @param {string} water_id      水印ID
     * @returns {void}
     */
    Watermark.prototype.updateWatermark = function (config, water_id) 
    {
        var water_id = water_id || 0,
            config = config || {},
            container = this.container,     //预览图
            watermark = $('#watermark' + water_id),  //获取对应 watermark_dom
            w = this.valuableWatermark(config['width'], container.width()) + 'px',  //宽度
            h = this.valuableWatermark(config['height'], container.height()) + 'px',  //高度
            dx = this.valuableWatermark(config['dx'], container.width()) + 'px',  //水平偏移
            dy = this.valuableWatermark(config['dy'], container.height()) + 'px';  //垂直偏移
            
        //设置水印图片路径
        watermark.attr({src: Wskeee.StringUtil.completeFilePath(config['url'])})
        // 如果宽是0并且高非0，则宽为空
        if(config['width'] == 0 && config['height'] != 0){
            w = '';
        }
        // 如果宽非0并且高为0，则高为空
        if(config['width'] != 0 && config['height'] == 0){
            h = '';
        }
        
        //判断水印的位置
        switch (config['refer_pos']) {
            case 'TopRight':
                watermark.css({
                    top: dy, right: dx, bottom: '', left: '', width: w, height: h
                });
                break;
            case 'TopLeft':
                watermark.css({
                    top: dy, right: '', bottom: '', left: dx, width: w, height: h
                });
                break;
            case 'BottomRight':
                watermark.css({
                    top: '', right: dx, bottom: dy, left: '', width: w, height: h
                });
                break;
            case 'BottomLeft':
                watermark.css({
                    top: '', right: '', bottom: dy, left: dx, width: w, height: h
                });
                break;
            default:
                watermark.css({top: dy, right: dx});
            }
    }
    
    /**
     * 删除水印
     * @param {string} water_id  水印ID
     * @return {void}
     */
    Watermark.prototype.removeWatermark = function(water_id)
    {
        var water_id = water_id || 0,
            watermark = $('#watermark' + water_id);  //获取对应 watermark_dom
    
        //删除元素
        watermark.remove();
    }
    
    /**
     * 验证数字 (0,1)[8,4096]
     * @param {Number} value    验证的数值
     * @param {Number} bgsize   背景宽高大小
     * @return {Number|@var;value}
     */
    Watermark.prototype.valuableWatermark = function(value, bgsize){
        value = Number(value);  //转为数字
        bgsize = Number(bgsize);//转为数字
        if (value < 8) {
            value = value <= 0 ? value = 0.12 : value;
            value = value > 1 ? value = 1 : value;
            value = value * bgsize;
        } else {
            value = value > 4096 ? value = 4096 : parseInt(value);
        }
        return value;
    }
    
    window.wate = window.wate || {};
    window.wate.Watermark = Watermark;
    
})(window, jQuery);