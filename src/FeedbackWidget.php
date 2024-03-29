<?php
namespace andrewdanilov\feedback;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class FeedbackWidget extends Widget
{
    public $controller;
    public $lightbox;
    public $jsCallback;
    public $jsErrorCallback;
    public $redirect;
    public $successMessage;
    public $submitButton;
    public $errorFieldClass;
    public $scrollToFirstError;
    public $errorFieldAlertElementClass;
    public $options;

    public function init()
    {
        parent::init();
        if (empty($this->successMessage)) {
            $this->successMessage = 'Message sent.';
        }
        if (empty($this->submitButton['name'])) {
            $this->submitButton['name'] = 'Send';
        }
        if (empty($this->submitButton['options']['class'])) {
            $this->submitButton['options']['class'] = 'btn btn-success';
        }
        if (empty($this->options)) {
            $this->options = [];
        }
        if (!isset($this->scrollToFirstError)) {
            $this->scrollToFirstError = true;
        }
    }

    public function run()
    {
        /* @var $controller FeedbackController */
        if (!empty($this->controller) && ($_controller = Yii::$app->createController($this->controller . '/send'))) {
            $controller = $_controller[0];
        } else {
            return false;
        }
        if (isset($controller->formView)) {
            $formView = $controller->formView;
        } else {
            return false;
        }
        if (isset($controller->fields)) {
            $fields = $controller->fields;
        } else {
            return false;
        }

        $model = new FeedbackForm();
        $widget_id = 'feedback-' . $this->id;
        $form_id = $widget_id . '-form';

        $options = [
            'id' => $form_id,
        ];

        $form = $this->render($formView, [
            'route' => '/' . $controller->id . '/send',
            'options' => array_merge($options, $this->options),
            'model' => $model,
            'fields' => $fields,
            'successMessage' => $this->successMessage,
            'submitButton' => $this->submitButton,
        ]);

        if (isset($this->lightbox)) {
            if (!isset($this->lightbox['label'])) {
                $this->lightbox['label'] = 'Feedback';
            }
            if (!isset($this->lightbox['dalay'])) {
                $this->lightbox['dalay'] = 4000;
            }
            if (!isset($this->lightbox['options'])) {
                $this->lightbox['options'] = [];
            }

            $this->lightbox['options']['data-fancybox'] = '';
            $this->lightbox['options']['data-src'] = '#' . $widget_id;

            if (isset($this->lightbox['button'])) {
                if ($this->lightbox['button'] === 'a') {
                    $this->lightbox['options']['href'] = 'javascript:;';
                }
                $button = Html::tag($this->lightbox['button'], $this->lightbox['label'], $this->lightbox['options']);
            } else {
                $button = '';
            }

            if (isset($this->lightbox['title'])) {
                $title = Html::tag('h2', $this->lightbox['title']);
            } else {
                $title = '';
            }

            $form_block = Html::tag('div', $title . $form, [
                'id' => $widget_id,
            ]);

            $hidden_wrapper = Html::tag('div', $form_block, [
                'style' => 'display:none;',
            ]);

            $out = $button . $hidden_wrapper;

            if (isset($this->lightbox['closeBtn'])) {
                // todo: replace $.fancybox.defaults to specific form option
                $this->getView()->registerJs("$.fancybox.defaults.btnTpl.smallBtn = '" . $this->lightbox['closeBtn'] . "';");
            }

            $this->getView()->registerJs("andrewdanilovFeedback.register('" . $form_id . "', '" . $this->redirect . "', true, " . $this->lightbox['delay'] . ");");

        } else {

            $out = $form;

            $this->getView()->registerJs("andrewdanilovFeedback.register('" . $form_id . "', '" . $this->redirect . "', false, false);");

        }

        if ($this->errorFieldClass) {
            $this->getView()->registerJs("andrewdanilovFeedback.error_field_class = '" . $this->errorFieldClass . "';");
        }
        if ($this->errorFieldAlertElementClass) {
            $this->getView()->registerJs("andrewdanilovFeedback.error_field_alert_element_class = '" . $this->errorFieldAlertElementClass . "';");
        }
        if ($this->jsCallback) {
            $this->getView()->registerJs("$(document).on('" . $widget_id . '-form-submit' . "', function(){" . $this->jsCallback . "});");
        }
        if ($this->jsErrorCallback) {
            $this->getView()->registerJs("$(document).on('" . $widget_id . '-form-error' . "', function(){" . $this->jsErrorCallback . "});");
        }
        $this->getView()->registerJs("andrewdanilovFeedback.scroll_to_first_error = " . ($this->scrollToFirstError ? 'true' : 'false') . ";");

        FeedbackAsset::register($this->getView());

        return $out;
    }
}