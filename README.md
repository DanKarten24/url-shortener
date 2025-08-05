
Протестировать: http://88.210.21.67:8089/



# URL Shortener на Yii2 + Docker

Простой сервис сокращения ссылок с аналитикой по переходам.

## Установка

```bash
git clone https://github.com/DanKarten24/url-shortener.git

cd url-shortener

cp env.example .env

docker compose up -d
```

## Статистика:
```sql
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
  month                                 AS `Месяц (перехода по ссылке)`,
  url                                   AS `Ссылка`,
  clicks                                AS `Кол-во переходов`,
  RANK() OVER (
    PARTITION BY month
    ORDER BY clicks DESC
  )                                     AS `Позиция в топе месяца по переходам`
FROM monthly_clicks
ORDER BY month DESC, `Позиция в топе месяца по переходам`;
```
