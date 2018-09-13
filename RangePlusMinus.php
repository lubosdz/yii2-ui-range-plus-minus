<?php
/**
* User numeric (integer, decimal) input by clicking on plus / minus icons increasing/decreasing the value by step optionally within specified min - max range.
*
* Usage example:
*
* use lubosdz\ui\RangePlusMinus;
*
* <?= $form->field($model, 'area_m2')->widget(RangePlusMinus::className(), [
* 	'unit' => 'm2',
* 	'min' => 10,
* 	'max' => 100,
* 	'tooHigh' => Yii::t('app', 'Maximum value is {max}.'),
* 	'tooLow' => Yii::t('app', 'Minimum value is {min}.'),
* 	'step' => 5,
* 	//'cssMinusIcon' => 'fa fa-minus',
* 	//'cssPlusIcon' => 'fa fa-plus',
* 	'options' => [
* 		'onchange' => new JsExpression('console.log(this)'),
* 	],
* ]) ?>
*
* <?= RangePlusMinus::widget([
*	'unit' => 'MHz',
*	'min' => 10,
*	'max' => 100,
*	'decimals' => 3,
*	'step' => 0.05,
*	'model' => $model,
*	'cssMinusButton' => 'btn btn-default',
*	'cssMinusIcon' => 'glyphicon glyphicon-chevron-down',
*	'cssPlusButton' => 'btn btn-default',
*	'cssPlusIcon' => 'glyphicon glyphicon-chevron-up',
*	'attribute' => 'frequency',
* ]) ?>
*/

namespace lubosdz\ui;

use Yii;
use yii\jui\InputWidget;
use yii\helpers\Html;
use yii\web\JsExpression;

class RangePlusMinus extends InputWidget
{
	/** @var string Appended value unit, e.g. Kg */
	public $unit;

	/** @var string Element cols width */
	public $cssWrapper = 'col-md-6';

	/** Formatting thousands + separator */
	public $thousandSep = '';
	public $decimalsSep = '.';

	/** @var float Minimal and maximal allowed values */
	public $min;
	public $max;
	public $defaultValue;

	/** @var string Error messages */
	public $tooHigh;
	public $tooLow;

	/** @var float Increase decrease step value */
	public $step = 1;

	/** @var int How many decimal places for rounding */
	public $decimals = 0;

	/** @var int 10, 100, 1000, ... roundup tens, hundreds, thousands, .. */
	public $roundPrecision = 0;

	/** @var string Any custom css */
	public $css = '';

	/** @var string CSS classes for plus/minus handles */
	public $cssMinusIcon = 'glyphicon glyphicon-minus';
	public $cssMinusButton = 'btn btn-success';
	public $cssPlusIcon = 'glyphicon glyphicon-plus';
	public $cssPlusButton = 'btn btn-danger';

	/**
	* @var array the HTML attributes for the container tag.
	* @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	*/
	public $containerOptions = [];

	// internal props
	protected $inputId;
	protected $key;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		if (!isset($this->containerOptions['id'])) {
			$this->containerOptions['id'] = $this->options['id'] . '-container';
		}

		if(!isset($this->options['class'])){
			$this->options['class'] = 'form-control';
		}

		if(!isset($this->options['maxlength'])){
			$this->options['maxlength'] = true;
		}

		// sync options with widget props
		$this->options['min'] = floatval($this->min);
		$this->options['max'] = floatval($this->max);
		$this->options['step'] = floatval($this->step);
		$this->options['decimals'] = intval($this->decimals);
		$this->options['thousandSep'] = $this->thousandSep;
		$this->options['defaultValue'] = trim($this->defaultValue); // allow also empty string
		$this->options['roundPrecision'] = intval($this->roundPrecision);

		// set default value
		if($this->defaultValue){
			if('' === trim($this->value)){
				$this->value = number_format(floatval($this->defaultValue), $this->decimals, $this->decimalsSep, $this->thousandSep);
			}
			if ($this->hasModel()) {
				$this->model->{$this->attribute} = number_format(floatval($this->defaultValue), $this->decimals, $this->decimalsSep, $this->thousandSep);
			}
		}elseif($this->hasModel() && $this->model->{$this->attribute}){
			$this->model->{$this->attribute} = number_format(floatval($this->model->{$this->attribute}), $this->decimals, $this->decimalsSep, $this->thousandSep);
		}elseif($this->value){
			$this->value = number_format(floatval($this->value), $this->decimals, $this->decimalsSep, $this->thousandSep);
		}

		if(!$this->tooHigh){
			$this->tooHigh = Yii::t('app', 'Value may not be higher than {max}.', ['max' => number_format(floatval($this->max), $this->decimals, $this->decimalsSep, $this->thousandSep)]);
		}
		if(!$this->tooLow){
			$this->tooLow = Yii::t('app', 'Value may not be lower than {min}.', ['min' => number_format(floatval($this->min), $this->decimals, $this->decimalsSep, $this->thousandSep)]);
		}

		if ($this->hasModel()) {
			$this->inputId = Html::getInputId($this->model, $this->attribute);
		}else{
			$this->inputId = Html::getInputId($this->name, $this->attribute);

		}

		$this->key = substr(md5($this->inputId), -6);

		if(!isset($this->options['onchange'])){
			$this->options['onchange'] = new JsExpression("rangePlusMinusClick_{$this->key}(this)");
		}
	}

	/**
	 * Executes the widget.
	 */
	public function run()
	{
		// row
		echo Html::beginTag('div', ['class' => 'row']);

		// label
		if(isset($this->options['label'])){
			echo Html::beginTag('div', ['class' => $this->cssWrapper]);
			echo Html::tag('label', $this->options['label'], ['for' => $this->inputId]);
			echo Html::endTag('div');
		}

		// element
		echo Html::beginTag('div', ['class' => $this->cssWrapper]);
		echo Html::beginTag('div', ['class' => 'input-group']);

		// minus
		echo Html::beginTag('span', ['class' => 'input-group-btn', 'action' => 'minus', 'target' => $this->inputId, 'onclick' => "rangePlusMinusClick_{$this->key}(this)"]);
		echo Html::beginTag('button', ['class' => $this->cssMinusButton, 'type' => 'button']);
		echo Html::tag('span', '', ['class' => $this->cssMinusIcon]);
		echo Html::endTag('button');
		echo Html::endTag('span');

		// input
		if ($this->hasModel()) {
			echo Html::activeTextInput($this->model, $this->attribute, $this->options);
		} else {
			echo Html::textInput($this->name, $this->value, $this->options);
		}

		if($this->unit){
			// insert unit as addon field
			echo Html::tag('span', $this->unit, ['class' => 'input-group-addon inside']);
		}

		// plus
		echo Html::beginTag('span', ['class' => 'input-group-btn', 'action' => 'plus', 'target' => $this->inputId, 'onclick' => "rangePlusMinusClick_{$this->key}(this)"]);
		echo Html::beginTag('button', ['class' => $this->cssPlusButton, 'type' => 'button']);
		echo Html::tag('span', '', ['class' => $this->cssPlusIcon]);
		echo Html::endTag('button');
		echo Html::endTag('span');

		echo Html::endTag('div'); // input-group
		echo Html::endTag('div'); // col-md-6
		echo Html::endTag('div'); // row

		// https://yii2-cookbook.readthedocs.io/forms-activeform-js/
		// https://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript
		$js = "
function rangePlusMinusClick_{$this->key}(caller){

	function roundUp(num, precision) {
		if(!precision || precision < 10){
			return num;
		}
		return Math.ceil(num / precision) * precision;
	}

	function numberFormat(x) {
		// format thousands separator
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'{$this->thousandSep}');
	}

	var me=$(caller),
		i=me.is('input') ? me : $('#'+me.attr('target')),
		origVal=$.trim(i.val().replace(/[^\d\. ]/g,'')),
		val=origVal.replace(/[^\d\.]/g,''),
		action=$.trim(me.attr('action')).toLowerCase(),
		step=parseFloat(i.attr('step')),
		min=parseFloat(i.attr('min')),
		max=parseFloat(i.attr('max')),
		defaultVal=parseFloat(i.attr('defaultValue') ? i.attr('defaultValue') : min),
		dec=parseInt(i.attr('decimals'),10);

	if(''===val){
		// first click on empty input
		val = defaultVal ? defaultVal.toFixed(dec) : min.toFixed(dec);
		val = roundUp(val, ".intval($this->roundPrecision).");
		val = numberFormat(val);
		i.val(val);
		if(!me.is('input')){
			i.trigger('change');
		}
		return;
	}

	val=parseFloat(val);
	if('minus' == action){
		val -= step;
	}else if('plus' == action){
		val += step;
	}

	val = roundUp(val, ".intval($this->roundPrecision).");
	if(max && val > max){
		val=max;
		i.closest('form').yiiActiveForm('updateAttribute', i.attr('id'), ['{$this->tooHigh}'.replace('{max}', numberFormat(max)+'".($this->unit ? ' '.$this->unit : '')."')]);
	}else if(min && val < min){
		val=min;
		i.closest('form').yiiActiveForm('updateAttribute', i.attr('id'), ['{$this->tooLow}'.replace('{min}', numberFormat(min)+'".($this->unit ? ' '.$this->unit : '')."')]);
	}else{
		i.closest('form').yiiActiveForm('updateAttribute', i.attr('id'), '');
	}

	val = val.toFixed(dec);
	val = numberFormat(val);
	if(val != origVal){
		i.val(val);
		if(!me.is('input')){
			i.trigger('change');
		}
	}
}
";
		/** \yii\web\View */
		$view = $this->getView();

		// register JS handlers
		$view->registerJs($js, $view::POS_HEAD);

		$view->registerCss("
.input-group-addon.inside{
	border-left: 0;
}
");
		// any custom CSS
		if($this->css){
			$view->registerCss($this->css);
		}
	}
}
