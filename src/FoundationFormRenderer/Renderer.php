<?php

namespace FoundationFormRenderer;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Renderer extends \Nette\Forms\Rendering\DefaultFormRenderer
{


	/**
	 * @var string
	 */
	protected $requiredLabel = 'Required';


	/**
	 * @param string $text
	 */
	public function setRequiredLabel($text)
	{
		$this->requiredLabel = $text;
	}


	/**
	 * Initializes form.
	 * @return void
	 */
	protected function init()
	{
		parent::init();

		foreach ($this->form->getControls() as $control) {
			if ($control instanceof \Nette\Forms\Controls\Button) {
				$control->getControlPrototype()->class = 'button small success radius';
			}
		}

		$this->wrappers['controls']['container'] = '';
		$this->wrappers['label']['container'] = '';
		$this->wrappers['control']['container'] = '';
		$this->wrappers['pair']['container'] = 'div class=row "';
	}


	/**
	 * Renders validation errors (per form or per control).
	 * @param \Nette\Forms\IControl $control
	 * @return string
	 */
	public function renderErrors(\Nette\Forms\IControl $control = NULL)
	{
		$errors = $control === NULL ? $this->form->getErrors() : $control->getErrors();
		if (count($errors)) {
			$wrapper = \Nette\Utils\Html::el('ul class="alert-box alert radius"');
			$wrapper->addAttributes(array('data-alert' => ''));
			$wrapper->add('<a href="#" class="close">&times;</a>');

			foreach ($errors as $error) {
				$item = \Nette\Utils\Html::el('li');
				$item->setHtml($error);
				$wrapper->add($item);
			}

			$blockWrapper = \Nette\Utils\Html::el('div class="large-12 columns"');
			$blockWrapper->setHtml($wrapper);

			$rowWrapper = \Nette\Utils\Html::el('div class="row"');
			$rowWrapper->setHtml($blockWrapper);

			return $rowWrapper->render(0);
		}
	}


	/**
	 * Renders single visual row.
	 * @param \Nette\Forms\IControl $control
	 * @return string
	 */
	public function renderPair(\Nette\Forms\IControl $control)
	{
		$pair = \Nette\Utils\Html::el('div class="large-12 columns"');

		$pair->add($this->renderLabel($control));
		$pair->add($this->renderControl($control));
		$pair->class($this->getValue($control->isRequired() ? 'pair .required' : 'pair .optional'), TRUE);
		$pair->class($control->getOption('class'), TRUE);

		if (++$this->counter % 2) {
			$pair->class($this->getValue('pair .odd'), TRUE);
		}

		$pair->id = $control->getOption('id');

		return $this->createWrapper($pair);
	}


	/**
	 * Renders 'label' part of visual row of controls.
	 * @param \Nette\Forms\IControl $control
	 * @return string
	 */
	public function renderLabel(\Nette\Forms\IControl $control)
	{
		if (!$control instanceof \Nette\Forms\Controls\Checkbox && !$control instanceof \Nette\Forms\Controls\Button) {
			if ($control->isRequired()) {
				$label = $control->getLabel();
				$caption = $label->getText();

				$required = \Nette\Utils\Html::el('small');
				$required->setText($this->requiredLabel);

				$label->setHtml($caption . " " . (string)$required);
				return $label;
			}
		}

		return parent::renderLabel($control);
	}


	/**
	 * Renders 'control' part of visual row of controls.
	 * @param \Nette\Forms\IControl $control
	 * @return string
	 */
	public function renderControl(\Nette\Forms\IControl $control)
	{
		if ($control instanceof \Nette\Forms\Controls\Checkbox) {
			$html = $control->getLabel();
			$caption = $html->getText();
			$html->setHtml((string)$control->getControl() . " " . $caption);
			return (string)$html;
		}

		return parent::renderControl($control);
	}


	/**
	 * Renders single visual row of multiple controls.
	 * @param \Nette\Forms\IFormControl[]
	 * @return string
	 * @throws \Nette\InvalidArgumentException
	 */
	public function renderPairMulti(array $controls)
	{
		$s = array();
		foreach ($controls as $control) {
			if (!$control instanceof \Nette\Forms\IControl) {
				throw new \Nette\InvalidArgumentException("Argument must be array of IFormControl instances.");
			}
			$s[] = (string) $control->getControl();
		}

		$pair = \Nette\Utils\Html::el('div class="large-12 columns"');
		$pair->add(trim($this->renderLabel($control)));
		$pair->add($this->getWrapper('control container')->setHtml(trim(implode(" ", $s))));

		return $this->createWrapper($pair);
	}


	/**
	 * @param string $elm
	 * @return string
	 */
	protected function createWrapper($elm)
	{
		$wrapper = \Nette\Utils\Html::el('div class=row');
		$wrapper->setHtml($elm);
		return $wrapper->render(0);
	}

}
