<?php
namespace andrewdanilov\feedback;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class FeedbackWidget extends Widget
{
	public $controller;
	public $fancybox;
	public $jsCallback;

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

		$model = new FeedbackForm();

		$out = $this->render($formTpl, [
			'controller' => $this->controller,
			'options' => [
				'data-feedback-form' => '',
				'id' => $this->id . '-form',
			],
			'model' => $model,
			'fields' => $fields,
		]);

		if (isset($this->fancybox)) {
			if (!isset($this->fancybox['button'])) {
				$this->fancybox['button'] = 'a';
			}
			if (!isset($this->fancybox['label'])) {
				$this->fancybox['label'] = 'Feedback';
			}
			if (!isset($this->fancybox['options'])) {
				$this->fancybox['options'] = [];
			}
			if ($this->fancybox['button'] === 'a') {
				$this->fancybox['options']['href'] = 'javascript:;';
			}
			$this->fancybox['options']['data-fancybox'] = '';
			$this->fancybox['options']['data-src'] = '#' . $this->id;

			$button = Html::tag($this->fancybox['button'], $this->fancybox['label'], $this->fancybox['options']);

			$form_block = Html::tag('div', $out, [
				'id' => $this->id,
				'data-lightbox' => '',
			]);

			$hidden_wrapper = Html::tag('div', $form_block, [
				'style' => 'display:none;',
			]);

			$out = $button . $hidden_wrapper;
		}

		if ($this->jsCallback) {
			$this->getView()->registerJs("$(document).on('" . $this->id . '-form-submit' . "', function(){" . $this->jsCallback . "})");
		}

		return $out;
	}
}