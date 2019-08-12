<?php

/** @var array $values */

use yii\helpers\Html;

?>

Письмо с сайта:<br /><br />

<?php foreach ($values as $value) { ?>
	<?php if ($value['value'] === 'on') { $value['value'] = 'Да'; } ?>
	<?php if (is_array($value['value'])) { ?>
		<?= Html::encode($value['label']) ?>: <b><?= Html::encode(implode(', ', $value['value'])) ?></b><br />
	<?php } else { ?>
		<?= Html::encode($value['label']) ?>: <b><?= Html::encode($value['value']) ?></b><br />
	<?php } ?>
<?php } ?>
