<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\SendMoneyForm */

$this->title = $user->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>



    <?= DetailView::widget([
        'model' => $user,
        'attributes' => [
            'username',
            'balance',
        ],
    ]) ?>
    
   
    <p>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'username')->widget(iPaya\fuelUX\ComboBox::class, 
            ['items' => $user->getOtherUsersArray()]
        ) ?> 
        <?= $form->field($model, 'amount') ?>
   
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'sender',
            'receiver',
            'amount',
            'realized',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>    
</div>
