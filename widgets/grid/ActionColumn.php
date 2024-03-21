<?php

namespace app\widgets\grid;

use yii\helpers\Html;

class ActionColumn extends \yii\grid\ActionColumn
{

//или
//.grid-view td.action-column {
//    white-space: nowrap;
//    text-align: center;
//    letter-spacing: 0.1em;
//    max-width: 7em;
//}

    public $template = '{update} {delete}';

    public $buttonOptions = ['class' => 'btn btn-icon'];
    public $header = 'Действия';
//    public $options = ['width' => '100'];
    public $headerOptions = ['class' => 'text-center'];
    public $contentOptions = [
        'class' => 'text-center',
        'style' => 'white-space: nowrap; text-align: center; letter-spacing: 0.1em; max-width: 7em;',
    ];

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        $this->icons = [

//            'pencil' => '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"/></svg>',
//            'trash' => '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"/></svg>'
            'pencil' => '<i class="glyphicon glyphicon-pencil"  style="color: #5cb85c;"></i>',
            'trash' => '<i class="glyphicon glyphicon-trash" style="color: #d9534f;"></i>',
        ];
        $this->initDefaultButtons();
    }

    /**
     * Инициализирует обратный вызов рендеринга кнопки по умолчанию для одной кнопки
     * @param string $name Название кнопки так, как оно написано в шаблоне
     * @param string $iconName Часть класса Bootstrap glyphicon, которая делает его уникальным
     * @param array $additionalOptions Множество дополнительных опций
     * @since 2.0.11
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = 'Просмотр';
                        break;
                    case 'update':
                        $title = 'Изменить';
                        break;
                    case 'delete':
                        $title = 'Удалить';
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                $icon = isset($this->icons[$iconName])
                    ? $this->icons[$iconName]
                    : Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
                return Html::a($icon, $url, $options);
            };
        }
    }
}