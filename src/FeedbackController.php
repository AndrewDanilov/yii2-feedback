<?php
namespace andrewdanilov\feedback;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class FeedbackController extends Controller
{
	public $mailTpl;
	public $formTpl;
	public $from = [];
	public $to = [];
	public $subject;
	public $jsCallback;
	public $fields = [];

	public function init()
	{
		parent::init();
		if (empty($this->mailTpl)) {
			$this->mailTpl = '@andrewdanilov/feedback/mail/default';
		}
		if (empty($this->formTpl)) {
			$this->formTpl = '@andrewdanilov/feedback/views/default';
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
				if ($model->load(Yii::$app->request->post())) {
					Yii::$app->response->format = Response::FORMAT_JSON;
					if ($model->sendFeedback($this->mailTpl, $this->from, $this->to, $this->subject, $this->fields)) {
						return [
							'success' => '1',
							'callback' => $this->jsCallback,
						];
					} else {
						return ['errors' => $model->errors];
					}
				}

			}
		}
		throw new BadRequestHttpException("Error request");
	}
}