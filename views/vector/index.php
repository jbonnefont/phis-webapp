<?php

//******************************************************************************
//                                       index.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 6 avr. 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  6 avr. 2018
// Subject: index of vectors (with search)
//******************************************************************************


use yii\helpers\Html;
use yii\grid\GridView;
use app\components\widgets\AnnotationButtonWidget;
use app\components\widgets\event\EventButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VectorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vector-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
            if (Yii::$app->session['isAdmin']) { ?>
        <?= Html::a(Yii::t('yii', 'Create') . ' ' . Yii::t('app', '{n, plural, =1{Vector} other{Vectors}}', ['n' => 1]), ['create'], ['class' => 'btn btn-success']) ?>
        <?php
            }
        ?>
    </p>
    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
              'attribute' => 'uri',
              'format' => 'raw',
               'value' => 'uri'
            ],
            'label',
            [
              'attribute' => 'rdfType',
              'format' => 'raw',
              'value' => function ($model) {
                return explode("#", $model->rdfType)[1];
              },
              'filter' => \kartik\select2\Select2::widget([
                    'attribute' => 'rdfType',
                    'model' => $searchModel,
                    'data' => $vectorsTypes,
                    'options' => [
                        'placeholder' => Yii::t('app/messages', 'Select type...'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
            ],
            'brand',
            'serialNumber',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} <br/> {event} {annotation}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['vector/view', 'id' => $model->uri]); 
                    },
                    'update' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 
                                        ['vector/update', 'id' => $model->uri]); 
                    },
                    'event' => function($url, $model, $key) {
                        return EventButtonWidget::widget([
                            EventButtonWidget::CONCERNED_ITEMS_URIS => [$model->uri],
                            EventButtonWidget::AS_LINK => true
                        ]); 
                    },
                    'annotation' => function($url, $model, $key) {
                        return AnnotationButtonWidget::widget([
                            AnnotationButtonWidget::TARGETS => [$model->uri],
                            AnnotationButtonWidget::AS_LINK => true
                        ]); 
                    },
                ]
            ],
        ],
    ]); ?>
</div>