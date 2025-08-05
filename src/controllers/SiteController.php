<?php

namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }

    /**
     * Показывает ТОП ссылок по переходам
     * @return string
     */
    public function actionTop()
    {
        $limit = \Yii::$app->request->get('limit', 20);
        $limit = is_numeric($limit) ? (int)$limit : 20;

        $sql = <<<SQL
WITH monthly_clicks AS (
    SELECT
        DATE_FORMAT(c.clicked_at, '%Y-%m') AS month,
        u.original_url                      AS url,
        COUNT(*)                            AS clicks
    FROM clicks c
    JOIN urls  u ON u.id = c.url_id
    GROUP BY month, url
)
SELECT
    month,
    url,
    clicks,
    RANK() OVER (
        PARTITION BY month
        ORDER BY clicks DESC
    ) AS position
FROM monthly_clicks
ORDER BY month DESC, position
LIMIT :limit
SQL;

        $rows = \Yii::$app->db->createCommand($sql, [':limit' => $limit])->queryAll();

        return $this->render('top', [
            'rows' => $rows,
        ]);
    }
} 