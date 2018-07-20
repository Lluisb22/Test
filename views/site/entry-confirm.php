<?php
use yii\helpers\Html;
?>
<p>We have sent an e-mail with the confirmation to the folliwing adress:</p>
<ul>
<li><label>Email</label>: <?= Html::encode($model->email) ?></li>
<li><label>Sent</label>: <?= Html::encode($model->name) ?></li>
</ul>