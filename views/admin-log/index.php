<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '操作记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="handle-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'title',
            [
                'attribute'=>'addtime',
                'value'=>function($model){
                    return date('Y-m-d H:i:s',$model->addtime);
                },
            ],
            ['class' => 'yii\grid\ActionColumn','template'=>'{view}']
        ],
        'tableOptions'=>['class' => 'table table-striped']
    ]); ?>

</div>