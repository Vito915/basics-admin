<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $customer_id            所属客户id
 * @property string $username               用户名
 * @property string $nickname               昵称或者真实名称
 * @property integer $type                  用户类型：1散户 2企业用户
 * @property string $password_hash          密码
 * @property string $password_reset_token   密码重置口令
 * @property int $sex                       姓别：0保密 1男 2女
 * @property string $phone                  电话
 * @property string $email                  邮箱
 * @property string $avatar                 头像
 * @property int $status                    状态：0 停用 10启用
 * @property bigint $max_store              最大存储空间（最小单位为B）
 * @property string $des                    简介
 * @property string $auth_key               认证
 * @property int $is_official 是否为官网资源：0否 1是
 * @property string $access_token           访问令牌
 * @property string $access_token_expire_time           访问令牌到期时间
 * @property string $created_at             创建时间
 * @property string $updated_at             更新时间
 * @property string $password write-only password
 * 
 * @property Customer $customer             客户
 * @property UserProfile $profile           用户配置属性
 */
class User extends BaseUser implements IdentityInterface {

    /**
     * 账号
     * @var array 
     */
    public static $statusIs = [
        self::STATUS_STOP => '停用',
        self::STATUS_ACTIVE => '启用',
    ];
    
    public function scenarios() {
        return [
            self::SCENARIO_CREATE =>
            ['username', 'nickname', 'sex', 'email', 'password_hash', 'password2', 'phone', 'avatar', 'des'],
            self::SCENARIO_UPDATE =>
            ['username', 'nickname', 'password_hash', 'password2', 'avatar'],
            self::SCENARIO_DEFAULT => 
            ['username', 'nickname', 'sex', 'email', 'password_hash', 'password2', 'phone', 'avatar', 'des'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['password_hash', 'password2'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['username', 'nickname', 'phone'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['username'], 'string', 'max' => 36, 'on' => [self::SCENARIO_CREATE]],
            [['username'], 'checkUsername', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['id', 'username'], 'unique'],
            [['password_hash'], 'string', 'min' => 6, 'max' => 64],
            [['created_at', 'updated_at', 'sex',], 'integer'],
            [['des'], 'string'],
            [['auth_key'], 'string', 'max' => 32],
            [['username', 'nickname'], 'string', 'max' => 50],
            [['phone'], 'string', 'min' => 11, 'max' => 50],
            [['password_reset_token', 'email', 'avatar'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 2],
            [['email'], 'email'],
            [['avatar'], 'image'],
            [['password2'], 'compare', 'compareAttribute' => 'password_hash'],
            [['avatar'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png'],
        ];
    }
    
    /**
     * 检查用户名是否为数字或字母及其组合
     * @param string $attribute username
     * @param string $params
     * @return boolean
     */
    public function checkUsername($attribute, $params)
    {
        $regex = '/[\x7f-\xff]/';
        if(preg_match($regex, $this->getAttribute($attribute))) {
            $this->addError($attribute, "用户名不能包含中文！"); 
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * 获取用户配置
     * @return ActiveQuery
     */
    public function getProfile(){
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }
}
