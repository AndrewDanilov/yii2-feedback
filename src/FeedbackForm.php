<?php
namespace andrewdanilov\feedback;

use Yii;
use yii\base\Model;

/**
 * FeedbackForm is the model behind the feedback form.
 */
class FeedbackForm extends Model
{
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

	public function init()
	{
		parent::init();
	}

	public function load($data, $formName = null)
	{
		return parent::load($data, $formName);
	}

	public function validateData($attribute, $value)
	{
		foreach ($this->fields as $field_name => $field) {
			if ($field['required'] && !$value[$field_name]) {
				$this->addError($field_name, 'Поле "' . $field['label'] . '" обязательно для заполнения.');
			}
			if ($field['max'] && $value[$field_name] > $field['max']) {
				$this->addError($field_name, 'Поле "' . $field['label'] . '" не может быть длиннее ' . $field['max'] . ' символов.');
			}
		}
	}

	/**
	 * Sends an email to the webmaster email address using the information collected by this model.
	 *
	 * @param $mailTpl string
	 * @param $from array|string
	 * @param $to array|string
	 * @param $subject string
	 * @param $fields array
	 * @return boolean
	 */
	public function sendFeedback($mailTpl, $from, $to, $subject, $fields)
	{
		$this->fields = $fields;
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
			$mailer = Yii::$app->mailer
				->compose($mailTpl, ['values' => $values])
				->setFrom($from)
				->setTo($to)
				->setSubject($subject);
			// отправляем письмо
			if ($mailer->send()) {
				return true;
			}
			throw new SendEmailException();
		}
		return false;
	}
}
