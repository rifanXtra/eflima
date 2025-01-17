<?php

use modules\account\web\admin\View;
use modules\crm\models\Customer;
use modules\project\models\forms\project\ProjectSearch;
use modules\project\models\Project;
use modules\project\models\ProjectStatus;
use modules\project\widgets\inputs\ProjectStatusDropdown;
use modules\task\widgets\inputs\TaskStatusDropdown;
use modules\ui\widgets\data_table\columns\ActionColumn;
use modules\ui\widgets\data_table\columns\CheckboxColumn;
use modules\ui\widgets\data_table\columns\DropdownColumn;
use modules\ui\widgets\data_table\DataTable;
use modules\ui\widgets\Icon;
use modules\ui\widgets\table\cells\Cell;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var View               $this
 * @var ActiveDataProvider $dataProvider
 * @var array              $dataTableOptions
 * @var ProjectSearch      $searchModel
 * @var true               $picker
 */
if (!isset($dataTableOptions)) {
    $dataTableOptions = [];
}

echo $this->block('@begin', [
    'dataTableOptions' => &$dataTableOptions,
]);
$statuses = ProjectStatus::find()->enabled()->createCommand()->queryAll();
$isInCustomer = !empty($searchModel->params['customer_id']);

$iconTypes = [
    Customer::TYPE_PERSONAL => 'i8:contacts',
    Customer::TYPE_COMPANY => 'i8:business-building',
];

$dataTable = DataTable::begin(ArrayHelper::merge([
    'dataProvider' => $dataProvider,
    'id' => 'project-data-table',
    'card' => false,
    'linkPager' => false,
    'idAttribute' => 'id',
    'lazy' => false,
    'columns' => [
        [
            'class' => CheckboxColumn::class,
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'content' => function ($model) {
                /** @var Project $model */

                $name = Html::a(Html::encode($model->name), ['/project/admin/project/view', 'id' => $model->id], [
                    'class' => 'data-table-primary-text',
                    'data-lazy-container' => '#main-container',
                    'data-lazy-modal' => 'project-view-modal',
                ]);

                return $name;
            },
        ],
        [
            'attribute' => 'customer_id',
            'format' => 'raw',
            'visible' => !$isInCustomer,
            'content' => function ($model) use ($iconTypes) {
                /** @var Project $model */

                if (!$model->customer_id) {
                    return '';
                }

                $type = Icon::show($iconTypes[$model->customer->type], [
                    'class' => 'icon icons8-size',
                    'title' => Yii::t('app', $model->customer->typeText),
                    'data-toggle' => 'tooltip',
                ]);
                $name = Html::a($type . Html::encode($model->customer->company_name), ['/crm/admin/customer/view', 'id' => $model->customer_id], [
                    'class' => 'd-block',
                    'data-lazy-container' => '#main-container',
                ]);

                if ($model->customer->type === Customer::TYPE_COMPANY) {
                    $primaryContact = Html::tag('div', Html::encode($model->customer->primaryContact->name), ['class' => 'data-table-secondary-text']);
                    $name .= $primaryContact;
                }

                return $name;
            },
        ],
        [
            'attribute' => 'started_date',
            'format' => 'raw',
            'headerCell' => [
                'hAlign' => Cell::H_ALIGN_CENTER,
            ],
            'contentCell' => [
                'hAlign' => Cell::H_ALIGN_CENTER,
                'vAlign' => Cell::V_ALIGN_CENTER,
            ],
            'content' => function ($model) {
                /** @var Project $model */

                $indicator = '';
                $isStarted = $model->isStarted;

                if ($isStarted) {
                    $indicator = Icon::show('i8:flash-on', ['class' => 'icons8-size mr-1 icon']);
                }

                $date = Html::tag('div', $indicator . Yii::$app->formatter->asDate($model->started_date), [
                    'class' => ($isStarted ? 'text-primary important' : ''),
                ]);
                $relativeTime = Html::tag(
                    'div',
                    Yii::$app->formatter->asRelativeTime($model->started_date),
                    ['class' => 'data-table-secondary-text ' . ($isStarted ? 'text-primary' : '')]
                );

                if ($isStarted) {
                    return Html::tag('div', $date . $relativeTime, [
                        'data-toggle' => 'tooltip',
                        'title' => Yii::t('app', 'Started'),
                    ]);
                }

                return $date . $relativeTime;
            },
        ],
        [
            'attribute' => 'deadline_date',
            'format' => 'raw',
            'headerCell' => [
                'hAlign' => Cell::H_ALIGN_CENTER,
            ],
            'contentCell' => [
                'hAlign' => Cell::H_ALIGN_CENTER,
                'vAlign' => Cell::V_ALIGN_CENTER,
            ],
            'content' => function ($model) {
                /** @var Project $model */

                $indicator = '';
                $isOverdue = $model->isOverdue;

                if ($isOverdue) {
                    $indicator = Icon::show('i8:error', ['class' => 'icons8-size mr-1 icon text-danger animation-blink']);
                }

                $date = Html::tag('div', $indicator . Yii::$app->formatter->asDate($model->deadline_date), [
                    'class' => ($isOverdue ? 'text-danger important' : ''),
                ]);
                $relativeTime = Html::tag(
                    'div',
                    Yii::$app->formatter->asRelativeTime($model->deadline_date),
                    ['class' => 'data-table-secondary-text ' . ($isOverdue ? 'text-danger' : '')]
                );

                if ($isOverdue) {
                    return Html::tag('div', $date . $relativeTime, [
                        'data-toggle' => 'tooltip',
                        'title' => Yii::t('app', 'Overdue'),
                    ]);
                }

                return $date . $relativeTime;
            },
        ],
        [
            'attribute' => 'status_id',
            'format' => 'raw',
            'headerCell' => [
                'hAlign' => Cell::H_ALIGN_CENTER,
            ],
            'contentCell' => [
                'hAlign' => Cell::H_ALIGN_CENTER,
                'vAlign' => Cell::V_ALIGN_CENTER,
            ],
            'content' => function ($model) {
                return ProjectStatusDropdown::widget([
                    'value' => $model->status_id,
                    'url' => function ($status) use ($model) {
                        return ['/project/admin/project/change-status', 'status' => $status['id'], 'id' => $model->id];
                    },
                ]);
            },
        ],
        [
            'class' => ActionColumn::class,
            'controller' => '/project/admin/project',
            'sort' => 1000000,
            'buttons' => [
                'update' => [
                    'visible' => Yii::$app->user->can('admin.project.update'),
                    'value' => [
                        'icon' => 'i8:edit',
                        'name' => Yii::t('app', 'Update'),
                        'data-lazy-container' => '#main-container',
                        'data-lazy-modal' => 'project-form-modal',
                        'data-toggle' => 'tooltip',
                    ],
                ],
                'view' => [
                    'visible' => Yii::$app->user->can('admin.project.view'),
                    'value' => [
                        'icon' => 'i8:eye',
                        'name' => Yii::t('app', 'View'),
                        'data-lazy-container' => '#main-container',
                        'data-lazy-modal' => 'project-view-modal',
                        'data-toggle' => 'tooltip',
                    ],
                ],
                'delete' => [
                    'visible' => Yii::$app->user->can('admin.project.delete'),
                    'value' => [
                        'icon' => 'i8:trash',
                        'label' => Yii::t('app', 'Delete'),
                        'data-confirmation' => Yii::t('app', 'You are about to delete {object_name}, are you sure', [
                            'object_name' => Yii::t('app', 'this item'),
                        ]),
                        'class' => 'text-danger',
                        'data-lazy-container' => '#main#',
                        'data-lazy-options' => ['scroll' => false, 'method' => 'DELETE'],
                    ],
                ],
            ],
        ],
    ],
], $dataTableOptions));

echo $this->block('@data-table');

DataTable::end();

echo $this->block('@end', $dataTable);
