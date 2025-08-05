<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "clicks".
 *
 * @property int $id
 * @property int $url_id
 * @property string $user_agent
 * @property string $clicked_at
 */
class Click extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clicks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['url_id', 'required'],
            ['url_id', 'integer'],
            ['user_agent', 'string'],
            ['clicked_at', 'safe'],
            ['url_id', 'exist', 'skipOnError' => true, 'targetClass' => Url::class, 'targetAttribute' => ['url_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url_id' => 'Url ID',
            'user_agent' => 'User Agent',
            'clicked_at' => 'Clicked At',
        ];
    }

    /**
     * Gets query for [[Url]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUrl()
    {
        return $this->hasOne(Url::class, ['id' => 'url_id']);
    }

    /**
     * Создает новую запись о клике
     */
    public static function createClick($urlId, $userAgent)
    {
        $click = new self();
        $click->url_id = $urlId;
        $click->user_agent = $userAgent;
        if ($click->save()) {
            return $click;
        }
        return false;
    }

    /**
     * Проверяет, является ли User-Agent ботом
     */
    public static function checkIfBot($userAgent, $shortCode = null)
    {
        if (empty($userAgent)) {
            return true;
        }

        try {
            $apiUrl = \Yii::$app->params['userAgentCheckUrl'] ?? 'http://qnits.net/api/checkUserAgent';
            $response = file_get_contents($apiUrl . '?userAgent=' . urlencode($userAgent));
            if ($response !== false) {
                $data = json_decode($response, true);
                if (isset($data['isBot'])) {
                    $isBot = (bool)$data['isBot'];
                    return $isBot;
                }
            }
        } catch (\Exception $e) {
        }
       return false;
    }
} 