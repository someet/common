<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Admin */

$this->title = '操作记录： '.$model->title;
$this->params['breadcrumbs'][] = ['label' => '操作记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'admin_name',
            'addtime:datetime',
            'admin_ip',
            'admin_agent',
            'controller',
            'action',
            'handle_id',
            'result'
        ],
    ]) ?>

</div>