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
		if (empty($this->subject)) {
			$this->subject = 'Mail from site';
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