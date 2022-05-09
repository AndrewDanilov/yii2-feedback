<?php
namespace andrewdanilov\feedback;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\UploadedFile;

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
				$error = $field['errors']['required'];
				$error = str_replace('{label}', $field['label'], $error);
				$this->addError($field_name, $error);
			}
			if (!empty($field['maxlength']) && mb_strlen($this->{$attribute}[$field_name]) > $field['maxlength']) {
				$error = $field['errors']['maxlength'];
				$error = str_replace('{label}', $field['label'], $error);
				$error = str_replace('{maxlength}', $field['maxlength'], $error);
				$this->addError($field_name, $error);
			}
			if (!empty($field['validator']) && is_callable($field['validator'])) {
				$result = call_user_func($field['validator'], $field_name, $this->{$attribute}[$field_name], $this->{$attribute});
				if ($result !== true) {
					if (!empty($result['error'])) {
						$this->addError($field_name, $result['error']);
					} else {
						$error = $field['errors']['error'];
						$error = str_replace('{label}', $field['label'], $error);
						$this->addError($field_name, $error);
					}
				}
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
            // if there is uploaded files - adding them to field values array
            if (!empty($_FILES)) {
                $this->data = array_merge($this->data, $_FILES);
            }
			foreach ($this->data as $key => $value) {
				if (array_key_exists($key, $this->fields)) {
					if (empty($this->fields[$key]['exclude'])) {
                        if ($this->fields[$key]['type'] === 'file') {
                            $uploadModel = new UploadFile();
                            if (isset($this->fields[$key]['maxFiles'])) {
                                $uploadModel->maxFiles = $this->fields[$key]['maxFiles'];
                            }
                            if (isset($this->fields[$key]['extensions'])) {
                                $uploadModel->extensions = $this->fields[$key]['extensions'];
                            }
                            if (isset($this->fields[$key]['uploadDir'])) {
                                $uploadModel->uploadDir = $this->fields[$key]['uploadDir'];
                            }
                            if (!empty($this->fields[$key]['multiple'])) {
                                $uploadModel->files = UploadedFile::getInstancesByName($key);
                            } else {
                                $uploadModel->files = array(UploadedFile::getInstanceByName($key));
                            }
                            if ($uploadModel->validate()) {
                                $basePath = Yii::getAlias('@webroot');
                                // converting paths to urls
                                $value = array_map(function($filePath) use ($basePath) {
                                    if (strpos($filePath, $basePath) === 0) {
                                        $fileUrl = substr($filePath, strlen($basePath));
                                        return Url::home(true) . ltrim($fileUrl, DIRECTORY_SEPARATOR);
                                    }
                                    return $filePath;
                                }, $uploadModel->upload());
                            } else {
                                foreach ($uploadModel->errors as $error) {
                                    $this->addError($key, $error);
                                }
                            }
                        }
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
			}
            // if there was no additive errors (i.e., on file uploads)
            if (!$this->hasErrors()) {
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
		}
		return false;
	}
}
