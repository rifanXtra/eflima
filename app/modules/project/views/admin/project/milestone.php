<?php

use modules\account\web\admin\View;
use modules\project\assets\admin\ProjectMilestoneAsset;
use modules\project\models\Project;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * @var View    $this
 * @var Project $model
 */

ProjectMilestoneAsset::register($this);

$this->subTitle = Yii::t('app', 'Milestone');
$this->fullHeightContent = true;

if (Yii::$app->user->can('admin.project.view.milestone.add')) {
    $this->toolbar['add-milestone'] = Html::a([
        'label' => Yii::t('app', 'Create Milestone'),
        'url' => ['/project/admin/project-milestone/add', 'project_id' => $model->id],
        'class' => 'btn btn-primary',
        'icon' => 'i8:plus',
        'data-lazy-modal' => 'project-milestone-form',
        'data-lazy-container' => '#main-container',
        'data-lazy-modal-size' => 'modal-md',
    ]);
}

$this->beginContent('@modules/project/views/admin/project/components/view-layout.php', [
    'model' => $model,
    'active' => 'milestone',
]);

echo $this->block('@begin');

?>
    <div class="px-3 h-100">
        <div id="project-milestone-<?= $this->uniqueId ?>" class="d-flex py-3 overflow-auto h-100">
            <?php
            foreach ($model->getMilestones()->orderBy('order')->all() AS $milestone) {
                echo $this->render('/admin/project-milestone/components/milestone-item', [
                    'model' => $milestone,
                ]);
            }
            ?>
        </div>
    </div>

<?php
echo $this->block('@end');

$jsOptions = Json::encode([
    'sortUrl' => Url::to(['/project/admin/project-milestone/sort', 'project_id' => $model->id]),
    'sortTaskUrl' => Url::to(['/project/admin/project-milestone/sort-task']),
    'moveTaskUrl' => Url::to(['/project/admin/project-milestone/move-task']),
    'loadTaskUrl' => Url::to(['/project/admin/project-milestone/task-list']),
]);

$this->registerJs("$('#project-milestone-{$this->uniqueId}').projectMilestone({$jsOptions})");

$this->endContent();
