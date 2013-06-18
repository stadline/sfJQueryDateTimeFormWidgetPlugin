<?php
/**
 * sfJQueryDateTimeValidator validates a datetime. It also converts the input value to a valid datetime.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Joseph Persie <persie.joseph@gmail.com>
 * @version    SVN: $Id: sfJQueryDateTimeValidator.class.php 28959 2012-04-20
 */
class sfJQueryDateTimeValidator extends sfValidatorBase
{
  private $value;
  private $format;
  
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * widget_name:             The name of the widget (to retrieve specific input value)
   *  * date_format:             The format to use when submitting a date with time (default to Y-m-d h:i A)
   *  * with_time:               true if the validator must return a time, false otherwise
   *  * date_output:             The format to use when returning a date (default to Y-m-d)
   *  * datetime_output:         The format to use when returning a date with time (default to Y-m-d H:i:s)
   *
   * Available error codes:
   *
   *  * missing_widget_name
   *
   * @param array $value    string value of the widget
   * @param array $format   string datetime format to return
   *
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('missing_widget_name', 'must provide widget_name as an option');

    $this->addOption('widget_name', $options['widget_name']);
    $this->addOption('date_format', 'Y-m-d h:i A');
    $this->addOption('with_time', true);
    $this->addOption('date_output', 'Y-m-d');
    $this->addOption('datetime_output', 'Y-m-d H:i:s');

    $this->_retrieveFormParams();
    $this->_initializeFormatting();
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value = null)
  {
      $date = DateTime::createFromFormat($this->getOption('date_format'),$this->value);
      $date->setTimezone(new DateTimeZone(date_default_timezone_get()));
      return $date->format($this->format);
  }

  private function _retrieveFormParams()
  {
      if(!$this->getOption('widget_name')) {
          throw new sfValidatorError($this, 'missing_widget_name',array());
      }

      $request = sfContext::getInstance()->getRequest();
      $param = $request->getParameter($this->getOption('widget_name'));
      $this->value = $param; 
  }

  private function _initializeFormatting()
  {
      if($this->getOption('with_time')) {
          $this->format = $this->getOption('datetime_output');
      }
      else {
          $this->format = $this->getOption('date_output');
      }
  }

  protected function isEmpty($value = null)
  {
      return empty($this->value);
  }
}
