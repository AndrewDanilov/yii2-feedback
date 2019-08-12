<?php
namespace andrewdanilov\feedback;

use Yii;
use yii\base\Widget;

class FeedbackWidget extends Widget
{
	public $controller;

	public function run()
	{
		if ($this->controller && isset(Yii::$app->controllerMap[$this->controller])) {
			$controller_conf = Yii::$app->controllerMap[$this->controller];
		} else {
			return false;
		}
		if (isset($controller_conf['formTpl'])) {
			$formTpl = $controller_conf['formTpl'];
		} else {
			return false;
		}
		if (isset($controller_conf['fields'])) {
			$fields = $controller_conf['fields'];
		} else {
			return false;
		}
		if (isset($controller_conf['jsCallback'])) {
			$jsCallback = $controller_conf['jsCallback'];
		} else {
			$jsCallback = false;
		}

		$model = new FeedbackForm();
		return $this->render($formTpl, [
			'controller' => $this->controller,
			'model' => $model,
			'fields' => $fields,
			'jsCallback' => $jsCallback,
		]);
	}
}