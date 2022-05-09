<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $route string */
/* @var $options array */
/* @var $model \andrewdanilov\feedback\FeedbackForm */
/* @var $fields array */
/* @var $successMessage string */
/* @var $submitButton array */

?>

<?php $form = ActiveForm::begin(['action' => [$route], 'options' => $options, 'enableClientValidation' => false]); ?>

<?php
foreach ($fields as $name => $field) {

	$options = [];
	if (isset($field['maxlength'])) {
		$options['maxlength'] = $field['maxlength'];
	}
	if (isset($field['placeholder'])) {
		$options['placeholder'] = $field['placeholder'];
	}
	if (!empty($field['required'])) {
		$options['required'] = '';
	}
	if (!empty($field['multiple'])) {
		$options['multiple'] = '';
	}

	if (!in_array($field['type'], ['hidden', 'radio', 'checkbox'])) {
		$options['class'] = 'form-control';
	} else {
		$options['class'] = '';
	}
	if (isset($field['class'])) {
		$options['class'] .= ' ' . $field['class'];
	}

	if (isset($field['style'])) {
		$options['style'] = $field['style'];
	}

	if (isset($field['default'])) {
		$model->data[$name] = $field['default'];
	}

	switch ($field['type']) {
		case 'text':
		case 'email':
		case 'tel':
		case 'numeric':
			$options['type'] = $field['type'];
			echo $form
				// placing fields variables into an array "data"
				->field($model, 'data[' . $name . ']')
				->label($field['label'])
				->textInput($options);
			break;
		case 'password':
			echo $form
				->field($model, 'data[' . $name . ']')
				->label($field['label'])
				->passwordInput($options);
			break;
		case 'hidden':
			echo $form
				->field($model, 'data[' . $name . ']')
				->hiddenInput($options);
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
		case 'file':
			if (isset($options['multiple'])) {
				// if multiple we need to accept array
                $name .= '[]';
			}
			echo $form
				// we can't place files variables into an array "data", because $_FILES is not associative array
				// but numeric, so we use direct variable name in attribute
				->field($model, $name)
                ->label($field['label'])
				->fileInput($options);
			break;
	}

}
?>

<div class="form-group">
	<?= Html::submitButton($submitButton['name'], $submitButton['options']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="form-success"><?= $successMessage ?></div>