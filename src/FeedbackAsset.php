<?php
namespace andrewdanilov\feedback;

use yii\web\AssetBundle;

class FeedbackAsset extends AssetBundle
{
	public $sourcePath = '@andrewdanilov/feedback/web';
	public $css = [
	];
	public $js = [
		'js/feedback.js',
	];
	public $depends = [
		'yii\web\JqueryAsset',
		'andrewdanilov\fancybox\FancyboxAsset',
	];
}
