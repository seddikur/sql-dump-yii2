<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $model mixed */
/* @var $buttons array массив кнопок */
/* @var $backUrl string URL для возврата при нажатии кнопки "Отмена" */

if (!isset($backUrl)) {
    $backUrl = '';
    if (defined(get_class($model) . '::URL_INDEX_ROUTE')) {
        $backUrl = Url::previous($model::URL_INDEX_ROUTE);
        if (empty($backUrl)) {
            $backUrl = Url::to([$model::URL_INDEX_ROUTE]);
        }
    }
    else {
        $previous = Url::previous();
        if (!empty($previous)) {
            $backUrl = $previous;
        }
        elseif (!empty(Yii::$app->request->referrer)) {
            $backUrl = Yii::$app->request->referrer;
        }
        elseif (!empty($model::URL_INDEX_ROUTE_AS_ARRAY)) {
            $backUrl = Url::to($model::URL_INDEX_ROUTE_AS_ARRAY);
        }
    }
}
?>
<div class="card-footer bg-light text-end p-3">
    <?php if (isset($buttons)): ?>
    <?php foreach ($buttons as $button): ?>
    <?= $button ?>

    <?php endforeach; ?>
    <?php else: ?>
    <?php if ($model instanceof \yii\db\ActiveRecord): ?>
    <?= $this->render('_submitButtonsStandard', ['model' => $model]) ?>

    <?php endif; ?>
    <?php endif; ?>
    <?php if (!empty($backUrl)): ?>
    или
    <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-sm btn-link']) ?>

    <?php endif; ?>
</div>
