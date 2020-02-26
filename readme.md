Yii2 Input widget for numbers with plus - minus handles
=======================================================

Widget for framework Yii2 which enables to collect numeric values within specified min - max range.
Supports increasing - decreasing the value by configured step and basic theming for bootstrap 3 or 4.

![Screenshot](http://static.synet.sk/screen-yii2-rangePlusMinus.gif)


Installation
============

```bash
$ composer require "lubosdz/yii2-ui-range-plus-minus" : "~1.0.0"
```

Usage
=====

```php

use lubosdz\ui\RangePlusMinus;

<?= $form->field($model, 'area_m2')->widget(RangePlusMinus::className(), [
	'bsVersion' => 3, // <-- enforce bootstrap 3 layout, since 1.0.3 is default BS4
	'unit' => 'm2',
	'min' => 10,
	'max' => 100,
	'tooHigh' => Yii::t('app', 'Maximum value is {max}.'),
	'tooLow' => Yii::t('app', 'Minimum value is {min}.'),
	'step' => 5,
	'cssClassMinus' => 'glyphicon glyphicon-minus',
	'cssClassPlus' => 'glyphicon glyphicon-plus',
	'options' => [
		'onchange' => new JsExpression('console.log(this)'),
	],
]) ?>

<?= RangePlusMinus::widget([
	'model' => $model,
	'attribute' => 'frequency',
	'unit' => 'MHz',
	'min' => 10,
	'max' => 100,
	'decimals' => 3,
	'step' => 0.05,
	'cssMinusButton' => 'bg-success text-white',
	'cssMinusIcon' => 'fa fa-chevron-down',
	'cssPlusButton' => 'bg-info text-white',
	'cssPlusIcon' => 'fa fa-chevron-up',
]) ?>

```

Widget options
==============

Option         |Description
---------------|---------------
unit           | Measure unit, e.g. kg, MHz
cssWrapper     | CSS class to set field width within a row, defaults to `col-md-12`
thousandSep    | Thousands separator, defaults to empty string
decimalsSep    | Decimals separator, defaults to comma `.`
min            | Minimum allowed value
max            | Maximum allowed value
step           | Value increased or decreased by this value, defaults to `1`.
defaultValue   | The value set on first load
tooHigh        | Error message to show when the new value is higher than max value
tooLow         | Error message to show when the new value is lower than min value
decimals       | How many decimals should be value formatted to, default `0`
roundPrecision | How should be value rounded, e.g. 100 will round to hundreds, 1000 will round the value to thousands etc, default `0`
css            | Any custom CSS string
cssMinusIcon   | CSS class for the minus icon, defaults to `fa fa-minus`
cssMinusButton | CSS class for the minus button, defaults to `bg-light` (gray background in BS4)
cssPlusIcon    | CSS class for the plus icon, defaults to `fa fa-plus`
cssPlusButton  | CSS class for the plus button, defaults to `bg-light`
cssUnitBg      | CSS class for the unit background color, defaults to `bg-light`


License
=======

This extension is open source and licensed under BSD-3-Clause (same as Yii2 framework).


Switching from BS3 to BS 4 - since 1.1.0
========================================

If you are using this widget for BS3, after upgrading to 1.1.0+ please set proper bootstrap version attribute `bsVersion = 3` - see example above.


Changelog
=======

* 1.1.0 - [26.02.2020] Added default support for BS4, fixed validation for 0, minor theming enhancements
* 1.0.2 - [13.09.2018] Fixed namespace
* 1.0.0 - [13.09.2018] Initial release
