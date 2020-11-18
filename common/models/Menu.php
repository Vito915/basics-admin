<?php

namespace common\models;

use common\modules\rbac\components\Helper;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property int $id ID
 * @property int $parent_id     父级ID
 * @property string $menu_name  菜单名称
 * @property string $alias_name 菜单别名
 * @property string $link       链接
 * @property string $icon       菜单图标
 * @property int $is_show       是否显示
 * @property int $is_jump       是否跳转
 * @property int $level         等级
 * @property int $sort_order    排序
 * @property int $is_del        是否删除：0否 1是
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 *
 * @property
 */
class Menu extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'is_show', 'is_jump', 'level', 'sort_order', 'is_del', 'created_at', 'updated_at'], 'integer'],
            [['menu_name', 'alias_name', 'icon'], 'string', 'max' => 60],
            [['link'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'menu_name' => Yii::t('app', '{Menu}{Name}', [
                'Menu' => Yii::t('app', 'Menu'),
                'Name' => Yii::t('app', 'Name'),
            ]),
            'alias_name' => Yii::t('app', 'Aliases'),
            'link' => Yii::t('app', 'Link'),
            'icon' => Yii::t('app', 'Icon'),
            'is_show' => Yii::t('app', 'Is Show'),
            'is_jump' => Yii::t('app', 'Is Jump'),
            'level' => Yii::t('app', 'Hierarchy'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'is_del' => Yii::t('app', '{Is}{Delete}', [
                'Is' => Yii::t('app', 'Is'),
                'Delete' => Yii::t('app', 'Delete'),
            ]),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * 保存之前
     * @param bool $insert
     * @return bool
     * @author Vitolao   <liuwenjun@eenet.com>
     * @date   2020/6/2
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //设置等级
            if (empty($this->parent_id)) {
                $this->parent_id = 0;
            }
            $this->level = $this->parent_id == 0 ? 0 : 1;
            return true;
        }
        return false;
    }


    /**
     * 关联获取父级
     * @return \yii\db\ActiveQuery
     * @author Vitolao   <liuwenjun@eenet.com>
     * @date   2020/6/1
     */
    public function getParent()
    {
        return $this->hasOne(Menu::class, ['id' => 'parent_id']);
    }

    /**
     * 获取菜单
     * @param array $condition    默认返回所有菜单
     * @return mixed
     * @author Vitolao   <liuwenjun@eenet.com>
     * @date   2020/6/1
     */
    public static function getMenus($condition = [])
    {
        return ArrayHelper::map(Menu::find()
            ->orFilterWhere($condition)->all(), 'id', 'menu_name');
    }

    /**
     * 获取对应等级的所有菜单
     * @param int $level
     * @return \yii\db\ActiveQuery
     * @author Vitolao   <liuwenjun@eenet.com>
     * @date   2020/6/1
     */
    public static function getMenusByLevel($level = 1)
    {
        return Menu::find()
            ->where(['is_show' => 1])
            ->andFilterWhere(['level' => $level])
            ->orderBy('sort_order ASC');
    }

    /**
     * 组装菜单
     * @return array
     * @author Vitolao   <liuwenjun@eenet.com>
     * @date   2020/6/1
     */
    public static function getMenuList()
    {
        $menus = self::getMenusByLevel(0)->all();

        $menuItems = [];
        foreach ($menus as $_menu) {
            if ($_menu->parent_id == 0) {
                $allMenus = self::getMenusByLevel()->all();
                $children = self::getItemChildren($allMenus, $_menu->id);
                $item = [
                    'label' => $_menu->menu_name,
                ];
                if (count($children) > 0) {
                    $item['url'] = $_menu->link;
                    $item['items'] = $children;
                } else {
                    $item['url'] = [$_menu->link];
                }
                $item['icon'] = $_menu->icon;
                $menuItems[] = $item;
            }
        }
        return $menuItems;
    }

    /**
     * 获取二级菜单
     * @param $allMenus
     * @param $parent_id
     * @return array
     * @author Vitolao   <liuwenjun@eenet.com>
     * @date   2020/6/1
     */
    private static function getItemChildren($allMenus, $parent_id)
    {
        $items = [];
        foreach ($allMenus as $menu) {
            /* @var $menu Menu */
            if ($menu->parent_id == $parent_id) {
                if (Helper::checkRoute($menu->link)) {
                    $items[] = [
                        'label' => $menu->menu_name,
                        'url' => [$menu->link],
                        'icon' => $menu->icon,
                    ];
                }
            }
        }

        return $items;
    }

}
