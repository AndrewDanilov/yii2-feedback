<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \andrewdanilov\feedback\FeedbackForm */
/* @var $fields array */

?>

<div class="feedback-form">

	<?php $form = ActiveForm::begin(); ?>

	<?php
	foreach ($fields as $name => $field) {

		$options = [];
		if (isset($field['maxlength'])) {
			$options['maxlength'] = $field['maxlength'];
		}
		if (isset($field['placeholder'])) {
			$options['placepholder'] = $field['placepholder'];
		}
		if (isset($field['class'])) {
			$options['class'] = $field['class'];
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
				echo $form
					->field($model, 'data[' . $name . ']')
					->label($field['label'])
					->radio($options);
				break;
			case 'checkbox':
				echo $form
					->field($model, 'data[' . $name . ']')
					->label($field['label'])
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

</div>
