<?php

namespace app\widgets\grid;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;


/**
 * Форма кнопок для ActiveForm
 *  пример вставки
 *   \app\widgets\grid\Submit::widget()
 */
class Submit extends Widget
{
    /**
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
//        $backUrl = Yii::$app->request->referrer;
        $output = Html::a('<i class="glyphicon glyphicon-menu-left"></i> Отмена', ['index'], [
            'class' => 'btn btn-default',
        ]);
        $output .= '  ';
        $output .= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Сохранить', ['class' => 'btn btn-success']);

        return $output;
    }
}
