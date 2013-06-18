<?php

class sfJQueryDateTimeWidget extends sfWidgetForm
{
    protected function configure($options = array(), $attributes = array())
    {
        $attributes['size']         = '8';
        $attributes['maxlength']    = '8';

        $this->addOption('widget_name');
        $this->addOption('params', false);
        $this->addOption('default_date', false);
        $this->addOption('default_time', false);
        $this->addOption('date_widget', new sfWidgetFormInput());
        $this->addOption('time_widget', new sfWidgetFormInput(array(),$attributes));
        $this->addOption('date_time', new sfWidgetFormInputHidden());
        $this->addOption('time_image', false);
        $this->addOption('time_start_stop', array());
        $this->addOption('time_interval', 15);

        parent::configure($options, $attributes);
    }

    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        $name = $this->getOption('widget_name');
        $dateWidget = $this->getOption('date_widget');
        $timeWidget = $this->getOption('time_widget');
        $dateTimeWidget = $this->getOption('date_time');
        $imageTime  = $this->getOption('time_image');
        $interval   = $this->getOption('time_interval');
        $start_stop = $this->getOption('time_start_stop');
        $code = '';

        $default = $this->getDefaults();

        if ($timeWidget instanceof sfWidgetFormInput) {
            $nameDate = $name . 'Date';
            $nameTime = $name . 'Time';
            $nameHide = $name;

            $buttonImageCode = '';
            if ($imageTime !== false) {
                $buttonImageCode = sprintf(', buttonImage: "%s", buttonImageOnly: true', $imageTime);
            }


            $idDate = $this->generateId($nameDate);
            $idTime = $this->generateId($nameTime);
            $idHide = $this->generateId($nameHide);

            $code .= $dateWidget->render($nameDate, $default['date'], $attributes, $errors);
            $code .= $dateTimeWidget->render($name, $default['both'], $attributes, $errors);

            if ((!isset($attributesTime['readonly'])) && (!isset($attributes['disabled']))) {
               $code .= sprintf(<<<EOF
<script type="text/javascript">
$(function(){
    $('#%s').datepicker({ dateFormat:'yy-mm-dd', showOn:'button', constrainInput:false%s });
    $('#%s').change(function() {
        var dt = Date.parse($(this).val());
        $(this).val((dt != null) ? dt.toString('yyyy-MM-dd') : '');
    });
    
    $(":input").change(function(){
        $("[name=%s]").val($("#%s").val() + ' ' + $("#%s").val());
    });
});

</script>
EOF
                ,
                $idDate,
                $buttonImageCode,
                $idDate,
                $idHide,
                $idDate,
                $idTime 
                );
            }
            
            if (!isset($start_stop['start'])) $start_stop['start'] = "00:00";
            if (!isset($start_stop['stop'])) $start_stop['stop'] = "23:" . (60 - $interval) ;
            
            $code .= $timeWidget->render($nameTime, $default['time'], $attributes, $errors);
            
            $code .= sprintf(<<<EOF
<script type="text/javascript">

$(function(){
    $("#%s").timePicker({
        startTime: "%s", // Using string. Can take string or Date object.
        endTime:   "%s", // Using Date object here.
        show24Hours: true,
        step: %s
    });
});
</script>
EOF
            ,
            $idTime,
            $start_stop['start'],
            $start_stop['stop'],
            $interval
            );
        }
        
        return $code;
    }

    private function getDefaults()
    {
        $default = array();
        
        $name = $this->getOption('widget_name');
        $params = $this->getOption('params');
        $default_date = $this->getOption('default_date');
        $default_time = $this->getOption('default_time');
        
        if(!empty($params[$name])) {
            $default['date'] = substr($params[$name], 0, 10);
            $default['time'] = substr($params[$name], -8);
        }
        else {
            $default['date'] = $default_date;
            $default['time'] = $default_time;
        }
        
        $default['both'] = implode(" ",$default);
        
        return $default;
    }
 
    public function getJavascripts()
    {
        return array(
            '/sfJQueryDateTimeFormWidgetPlugin/timePicker/jquery.timePicker.js',
        );
    }
 
    public function getStylesheets()
    {
        return array(
            '/sfJQueryDateTimeFormWidgetPlugin/timePicker/timePicker.css' => 'screen',
        );
    }
}
