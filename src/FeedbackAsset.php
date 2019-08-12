<?php
namespace andrewdanilov\feedback;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class FeedbackAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css = [];
	public $js = [
		'/js/feedback.js',
	];
	public $depends = [
		'yii\web\JqueryAsset',
	];
}
