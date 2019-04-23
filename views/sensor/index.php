<?php

//******************************************************************************
//                                       index.php
//
// Author(s): Morgane Vidal <morgane.vidal@inra.fr>
// PHIS-SILEX version 1.0
// Copyright © - INRA - 2018
// Creation date: 29 mars 2018
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
// Last modification date:  29 mars 2018
// Subject: index of sensors (with search)
//******************************************************************************

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SensorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 2]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensor-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
            if (Yii::$app->session['isAdmin']) {
                echo Html::a(Yii::t('yii', 'Create') . ' ' . Yii::t('app', '{n, plural, =1{Sensor} other{Sensors}}', ['n' => 1]), ['create'], ['class' => 'btn btn-success']) . "\t";
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
               'value' => 'uri',
              'filter' =>false,
            ],
            'label',
            [
              'attribute' => 'rdfType',
              'format' => 'raw',
              'value' => function ($model) {
                return explode("#", $model->rdfType)[1];
              }
            ],
            'brand',
            'serialNumber',
            'model',
            [
              'attribute' => 'inServiceDate',
              'format' => 'raw',
               'value' => 'inServiceDate',
              'filter' => DatePicker::widget([
                    'model' => $searchModel, 
                    'attribute' => 'inServiceDate',
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]),
            ],
            [
              'attribute' => 'dateOfLastCalibration',
              'format' => 'raw',
               'value' => 'dateOfLastCalibration',
              'filter' => DatePicker::widget([
                    'model' => $searchModel, 
                    'attribute' => 'dateOfLastCalibration',
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]),
            ],
            [
              'attribute' => 'personInCharge',
              'format' => 'raw',
              'value' => function ($model, $key, $index) {
                    return Html::a($model->personInCharge, ['user/view', 'id' => $model->personInCharge]);
                },
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                        ['sensor/view', 'id' => $model->uri]); 
                    },
                ]
            ],
        ],
    ]); ?>
</div>