<?php

/**
 *
 * SegBox.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2014, Segworks Technologies Corporation
 */

Yii::import('bootstrap.widgets.TbBox');

/**
 * Extension of Yii-booster's TBBox widget. Provides additional widget
 * features.
 *
 * @package application.widgets
 */
class SegBox extends TbBox {

    /**
     *
     * @var string
     */
    public $footer = false;

    /**
     *
     * @var array
     */
    public $htmlFooterOptions = array();

    /**
     * Widget initialization
     */
    public function init() {
        if (isset($this->htmlFooterOptions['class'])) {
			$this->htmlFooterOptions['class'] = 'bootstrap-widget-footer ' . $this->htmlFooterOptions['class'];
		} else {
			$this->htmlFooterOptions['class'] = 'bootstrap-widget-footer';
		}
        parent::init();
    }

    	/**
	 *### .renderHeader()
	 *
	 * Renders the header of the box with the header control (button to show/hide the box)
	 */
	public function renderHeader()
	{
		if ($this->title !== false) {
			echo CHtml::openTag('div', $this->htmlHeaderOptions);
			if ($this->title) {
				$this->title = '<h3>' . $this->title . '</h3>';

				if ($this->headerIcon) {
					$this->title = '<i class="' . $this->headerIcon . '"></i>' . $this->title;
				}

				echo $this->title;
			}

            $this->renderButtons();
			echo CHtml::closeTag('div');
		}
	}

    /**
	 *### .renderButtons()
	 *
	 * Renders a header buttons to display the configured actions
	 */
	public function renderButtons()
	{
		if (empty($this->headerButtons)) {
			return;
		}

		echo '<div class="bootstrap-toolbar ' . ($this->title ? 'pull-right' : '') . '">';

		if (!empty($this->headerButtons) && is_array($this->headerButtons)) {
			foreach ($this->headerButtons as $button) {
				$options = $button;
				$button = $options['class'];
				unset($options['class']);

				if (strpos($button, 'TbButton') === false) {
					throw new CException('message');
				}

				if (!isset($options['htmlOptions'])) {
					$options['htmlOptions'] = array();
				}

				$class = isset($options['htmlOptions']['class']) ? $options['htmlOptions']['class'] : '';
				$options['htmlOptions']['class'] = $class . ($this->title ? ' pull-right' : '');

				$this->controller->widget($button, $options);
			}
		}

		echo '</div>';
	}

    /**
     *
     */
    public function renderContentEnd() {
        parent::renderContentEnd();
        if (!empty($this->footer)) {
            echo CHtml::tag('div', $this->htmlFooterOptions, $this->footer);
        }
	}
}

