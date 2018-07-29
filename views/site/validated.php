
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<p>Your account is already active. Please add password to finish your access process:</p>
<p><?=Html::encode($model->token);?></p>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'password')->passwordInput() ?>
<?= $form->field($model, 'repeat_password')->passwordInput() ?>
<div class="form-group">
<?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>