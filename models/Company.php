<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property string $name 公司名称
 * @property string $address 公司地址
 * @property string $phone_call 公司电话
 * @property string $user_id 用户标示
 * @property string $remarks 描述
 * @property int $street_id 所属街道
 * @property int $id
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remarks'], 'string'],
            [['street_id'], 'integer'],
            [['name', 'user_id'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 200],
            [['phone_call'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'address' => 'Address',
            'phone_call' => 'Phone Call',
            'user_id' => 'User ID',
            'remarks' => 'Remarks',
            'street_id' => 'Street ID',
            'id' => 'ID',
        ];
    }
}
