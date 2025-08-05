<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use app\models\Url;
use app\models\Click;

class UrlController extends Controller
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            // Отключаем CSRF для API
            $this->enableCsrfValidation = false;
            return true;
        }
        return false;
    }
    /**
     * Создание короткой ссылки
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
     
        $originalUrl = $request->get('original_url');
        
        if (empty($originalUrl)) {
            return [
                'success' => false,
                'error' => 'Original URL is required'
            ];
        }
        
        // Валидация URL
        if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            return [
                'success' => false,
                'error' => 'Invalid URL format'
            ];
        }
        
        try {
            $url = Url::createShortUrl($originalUrl);
            
            if ($url) {
                $shortUrl = Yii::$app->request->hostInfo . '/' . $url->short_code;
                
                return [
                    'success' => true,
                    'short_url' => $shortUrl,
                    'original_url' => $url->original_url,
                    'short_code' => $url->short_code
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to create short URL'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ];
        }

    }

    /**
     * Редирект по короткой ссылке
     */
    public function actionRedirect($shortCode)
    {
        $url = Url::findByShortCode($shortCode);

        if (!$url) {
            throw new NotFoundHttpException('Short URL not found');
        }

        $userAgent = Yii::$app->request->userAgent;

        // Мгновенно отправляем редирект клиенту
        $response = Yii::$app->response;
        $response->redirect($url->original_url);
        $response->send();

        /* 
        Сделан простой вариант для асинхронного редиректа,
        можно сделать лучше, например, через yii2-queue
        */

        // Завершаем вывод для FastCGI, если возможно
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        // Проверяем, является ли пользователь ботом
        $isBot = Click::checkIfBot($userAgent, $url->short_code);

        // Сохраняем клик только если это не бот
        if (!$isBot) {
            Click::createClick($url->id, $userAgent);
        }

        // Полностью завершаем выполнение скрипта
        Yii::$app->end();
    }


} 