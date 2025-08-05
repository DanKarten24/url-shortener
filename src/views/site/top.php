<?php
/* @var $this yii\web\View */
/* @var $rows array */

$this->title = 'Статистика';
?>

<h1>Статистика</h1>

<?php if (empty($rows)): ?>
    <p>Нет данных для отображения.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Месяц (перехода по ссылке)</th>
                <th>Ссылка</th>
                <th>Кол-во переходов</th>
                <th>Позиция в топе месяца</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['month']) ?></td>
                <td>
                    <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank">
                        <?= htmlspecialchars($row['url']) ?>
                    </a>
                </td>
                <td><?= (int)$row['clicks'] ?></td>
                <td><?= (int)$row['position'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
