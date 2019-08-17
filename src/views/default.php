<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $controller string */
/* @var $options array */
/* @var $model \andrewdanilov\feedback\FeedbackForm */
/* @var $fields array */
/* @var $success_message string */

?>

<?php $form = ActiveForm::begin(['action' => [$controller . '/send'], 'options' => $options, 'enableClientValidation'=>false]); ?>

<?php
foreach ($fields as $name => $field) {

	$options = [];
	if (isset($field['maxlength'])) {
		$options['maxlength'] = $field['maxlength'];
	}
	if (isset($field['placeholder'])) {
		$options['placepholder'] = $field['placepholder'];
	}
	$options['class'] = 'form-control';
	if (isset($field['class'])) {
		$options['class'] .= ' ' .$field['class'];
	}

	if (isset($field['default'])) {
		$model->data[$name] = $field['default'];
	}

	switch ($field['type']) {
		case 'text':
			echo $form
				->field($model, 'data[' . $name . ']')
				->label($field['label'])
				->textInput($options);
			break;
		case 'textarea':
			echo $form
				->field($model, 'data[' . $name . ']')
				->label($field['label'])
				->textarea($options);
			break;
		case 'radio':
			$options['label'] = $field['label'];
			echo $form
				->field($model, 'data[' . $name . ']')
				->radio($options);
			break;
		case 'checkbox':
			$options['label'] = $field['label'];
			echo $form
				->field($model, 'data[' . $name . ']')
				->checkbox($options);
			break;
		case 'select':
			echo $form
				->field($model, 'data[' . $name . ']')
				->label($field['label'])
				->dropDownList($field['items'], $options);
			break;
	}

}
?>

<div class="form-group">
	<?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="form-success"><?= $success_message ?></div>