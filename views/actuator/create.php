<?php

//******************************************************************************
//                                       create.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 19 avr. 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YiiActuatorModel */

$this->title = Yii::t('yii', 'Add Actuators');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Actuator} other{Actuators}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="actuator-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'actuatorsTypes' => $actuatorsTypes,
        'users' => $users
    ]) ?>
</div>