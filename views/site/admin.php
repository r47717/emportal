<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Администрирование пользователей';
$this->params['breadcrumbs'][] = $this->title;


function filter_is_ok($values, $filterstate, $userrolesfilter)
{
	return
		(!$filterstate['name']  or strpos($values['name'], $filterstate['name']) !== false) and
		(!$filterstate['phone'] or strpos($values['phone'], $filterstate['phone']) !== false) and
		(!$filterstate['email'] or strpos($values['email'], $filterstate['email']) !== false) and
		($filterstate['role'] == '2'  or $values['role'] == $userrolesfilter[$filterstate['role']]);
}

?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (Yii::$app->session->hasFlash('userDeleted')): ?>
    	<div class="alert alert-success">
            Пользователь <?= Yii::$app->session->getFlash('userDeleted') ?> удалён из базы данных
        </div>
    <?php endif; ?>
    <br>

    <table class="table">
    	<?= Html::beginForm(['site/admin'], 'post') ?>
    	<tr>
    		<th>Id</th>
    		<th>Имя&nbsp;<?= Html::textInput('name', $filterstate['name'], ['class' => '', 'style' => 'width: 120px']) ?></th>
    		<th>Телефон&nbsp;<?= Html::textInput('phone', $filterstate['phone'], ['class' => '', 'style' => 'width: 120px']) ?></th>
    		<th>Email&nbsp;<?= Html::textInput('email', $filterstate['email'], ['class' => '', 'style' => 'width: 120px']) ?></th>
    		<th>Роль&nbsp;<?= Html::dropDownList('role', $filterstate['role'], $userrolesfilter, ['class' => '', 'style' => 'width: 140px']) ?></th>
    		<th><?= Html::submitButton('<span class="glyphicon glyphicon-refresh" />&nbsp;Обновить', ['class' => 'btn btn-sm btn-info', 'name' => 'refresh']) ?></th>
    	</tr>
    	<?= Html::endForm() ?>

	    <?php foreach ($users as $id => $values): ?>
	    	<?= Html::beginForm(['site/adminuser'], 'post') ?>
	    	<?php if(filter_is_ok($values, $filterstate, $userrolesfilter)): ?>
	    	<tr>
	    		<td><?= $values['id'] ?></td>
	    		<td><input type="text" name='name' value="<?= $values['name'] ?>" /></td>
	    		<td><input type="text" name='phone' value="<?= $values['phone'] ?>" /></td>
	    		<td><input type="text" name='email' value="<?= $values['email'] ?>" /></td>
	    		<td>
	    			<select name="role">
	    				<?php foreach ($userroles as $role): ?>
	    					<?php $selected = ($values['role'] == $role) ? "selected" : "" ?>
	    					<option value="<?= $role ?>" <?= $selected ?> ><?= $role ?></option>
	    				<?php endforeach; ?>
	    			</select>
	    		</td>
	    		<td>
	    			<?= Html::submitButton('<span class="glyphicon glyphicon-floppy-disk" />&nbsp;Сохранить', 
	    				['class' => 'btn btn-sm btn-primary', 'name' => 'update']) ?>
	    			<?= Html::submitButton('<span class="glyphicon glyphicon-remove" />&nbsp;Удалить', 
	    				['class' => 'btn btn-sm btn-warning', 'name' => 'delete']) ?>
	    			<?= Html::hiddenInput('id', $values['id']) ?>
	    		</td>
	    	</tr>
	    	<?php endif; ?>
	    	<?= Html::endForm() ?>
		<?php endforeach ?>
    </table>
    <p>
    	<?= Html::beginForm(['/site/newuser'], 'post') ?>

		<?= Html::submitButton('<span class="glyphicon glyphicon-plus" />&nbsp;Добавить', ['class' => 'btn btn-sm btn-success']) ?>
		<?= Html::endForm() ?>
    </p>
</div>
