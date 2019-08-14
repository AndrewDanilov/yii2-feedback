<?php
namespace andrewdanilov\feedback;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class FeedbackAsset extends AssetBundle
{
	public $sourcePath = '@andrewdanilov/feedback/web';
	public $css = [
		'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css',
	];
	public $js = [
		'js/feedback.js',
		'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js',
	];
	public $depends = [
		'yii\web\JqueryAsset',
	];
}
