<?php
namespace andrewdanilov\feedback;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class FeedbackController extends Controller
{
	public $formView;
	public $mailView;
	public $mailLayout;
	public $extraFieldLabel;
	public $from = [];
	public $to = [];
	public $subject;
	public $fields = [];

	public function init()
	{
		parent::init();
		if (empty($this->formView)) {
			$this->formView = '@andrewdanilov/feedback/views/default';
		}
		if (empty($this->mailView)) {
			$this->mailView = '@andrewdanilov/feedback/mail/default';
		}
		if (empty($this->mailLayout)) {
			$this->mailLayout = '@andrewdanilov/feedback/mail/layouts/html';
		}
		if (empty($this->extraFieldLabel)) {
			$this->extraFieldLabel = 'Extra';
		}
		if (empty($this->subject)) {
			$this->subject = 'Mail from site';
		}
		foreach ($this->fields as $key => $field) {
			if (!is_array($field)) {
				// convert simple elements to array-notation
				$this->fields[$field] = ['type' => 'text', 'label' => mb_strtoupper($field)];
				// remove old element
				unset($this->fields[$key]);
			} else {
				if (empty($this->fields[$key]['type'])) {
					// type key must exist, set it to default if not
					$this->fields[$key]['type'] = 'text';
				}
				if (empty($this->fields[$key]['label'])) {
					// label key must exist, set it to default if not
					$this->fields[$key]['label'] = mb_strtoupper($key);
				}
				// set default error templates
				if (empty($this->fields[$key]['errors']['required'])) {
					$this->fields[$key]['errors']['required'] = 'Field "{label}" is required.';
				}
				if (empty($this->fields[$key]['errors']['maxlength'])) {
					$this->fields[$key]['errors']['maxlength'] = 'Field "{label}" length of {maxlength} exeeded.';
				}
				if (empty($this->fields[$key]['errors']['error'])) {
					$this->fields[$key]['errors']['error'] = 'Field "{label}" is incorrect.';
				}
			}
		}
	}

	/**
	 * Отправка писем
	 *
	 * @return array
	 * @throws BadRequestHttpException
	 */
	public function actionSend()
	{
		if (Yii::$app->request->isAjax) {
			if (Yii::$app->request->isPost) {

				$model = new FeedbackForm();
				$model->mailView = $this->mailView;
				$model->mailLayout = $this->mailLayout;
				$model->extraFieldLabel = $this->extraFieldLabel;
				$model->fields = $this->fields;
				if ($model->load(Yii::$app->request->post(), '')) {
					Yii::$app->response->format = Response::FORMAT_JSON;
					if ($model->sendFeedback($this->from, $this->to, $this->subject)) {
						return ['success' => '1'];
					} else {
						return ['errors' => $model->errors];
					}
				}

			}
		}
		throw new BadRequestHttpException("Error request");
	}
}