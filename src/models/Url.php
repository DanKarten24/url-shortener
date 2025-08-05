<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "urls".
 *
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property string $created_at
 * @property string $updated_at
 */
class Url extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'urls';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            // TimestampBehavior убран, так как MySQL автоматически устанавливает created_at и updated_at
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['original_url', 'short_code'], 'required'],
            [['original_url'], 'string'],
            [['short_code'], 'string', 'max' => 10],
            [['short_code'], 'unique'],
            [['original_url'], 'url'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'original_url' => 'Original URL',
            'short_code' => 'Short Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Генерирует уникальный короткий код
     */
    public static function generateShortCode()
    {
        $attempts = 0;
        $maxAttempts = 10;
        
        do {
            $code = self::generateRandomCode();
            $exists = self::find()->where(['short_code' => $code])->exists();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);
        
        if ($attempts >= $maxAttempts) {
            throw new \Exception('Unable to generate unique short code');
        }
        
        return $code;
    }

    /**
     * Генерирует случайный код из 5 символов
     */
    private static function generateRandomCode()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        
        for ($i = 0; $i < 5; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        return $code;
    }

    /**
     * Создает новую короткую ссылку
     */
    public static function createShortUrl($originalUrl)
    {
        // Проверяем, существует ли уже такая ссылка
        $existingUrl = self::find()->where(['original_url' => $originalUrl])->one();
        
        if ($existingUrl) {
            return $existingUrl;
        }
        
        $url = new self();
        $url->original_url = $originalUrl;
        $url->short_code = self::generateShortCode();
        
        if ($url->save()) {
            return $url;
        }
        
        return false;
    }

    /**
     * Находит URL по короткому коду
     */
    public static function findByShortCode($shortCode)
    {
        return self::find()->where(['short_code' => $shortCode])->one();
    }

    /**
     * Получает статистику кликов
     */
    public function getClicks()
    {
        return $this->hasMany(Click::class, ['url_id' => 'id']);
    }

    /**
     * Получает количество кликов (исключая ботов)
     */
    public function getClicksCount()
    {
        return $this->getClicks()->count();
    }
} 