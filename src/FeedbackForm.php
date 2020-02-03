<?php
namespace andrewdanilov\feedback;

use Yii;
use yii\base\Model;

/**
 * FeedbackForm is the model behind the feedback form.
 */
class FeedbackForm extends Model
{
	public $mailView;
	public $mailLayout;
	public $extraFieldLabel;

	public $fields = [];
	public $data = [];

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['data'], 'validateData'],
		];
	}

	public function formName()
	{
		return '';
	}

	public function validateData($attribute, $params)
	{
		foreach ($this->fields as $field_name => $field) {
			if (!empty($field['required']) && !$this->{$attribute}[$field_name]) {
				$this->addError($field_name, 'Поле "' . $field['label'] . '" обязательно для заполнения.');
			}
			if (!empty($field['maxlength']) && mb_strlen($this->{$attribute}[$field_name]) > $field['maxlength']) {
				$this->addError($field_name, 'Поле "' . $field['label'] . '" не может быть длиннее ' . $field['maxlength'] . ' символов.');
			}
		}
	}

	/**
	 * Sends an email to the webmaster email address using the information collected by this model.
	 *
	 * @param $from array|string
	 * @param $to array|string
	 * @param $subject string
	 * @return boolean
	 */
	public function sendFeedback($from, $to, $subject)
	{
		if ($this->validate()) {
			$values = [];
			foreach ($this->data as $key => $value) {
				if (array_key_exists($key, $this->fields)) {
					if (isset($this->fields[$key]['label'])) {
						$label = $this->fields[$key]['label'];
					} else {
						$label = $key;
					}
					$values[] = [
						'label' => $label,
						'value' => $value,
					];
				}
			}
			if (isset($this->data['extra'])) {
				$values[] = [
					'label' => $this->extraFieldLabel,
					'value' => $this->data['extra'],
				];
			}
			$mailer = Yii::$app->mailer;
			$mailer->htmlLayout = $this->mailLayout;
			$message = $mailer->compose($this->mailView, ['values' => $values])
				->setFrom($from)
				->setTo($to)
				->setSubject($subject);
			// отправляем письмо
			if ($message->send()) {
				return true;
			}
		}
		return false;
	}
}
