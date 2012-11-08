<?php

defined('_JEXEC') or die('Restricted Access');
$session = JFactory::getSession();
$module_name = str_replace('moo_', '', basename(dirname(__FILE__)));

$class = str_replace(' ', '', ucwords(str_replace(
        '_', ' ', $module_name
    )
));

$template_class = $class . 'Template';
if (!class_exists($class)) {
    require_once('helper.php');
    $class = new $class;
    $class::initialize();
    $class::loadAssets();
} 

require_once('template.php');
$template = new $template_class;

if (empty($_POST) &&
    !$session->get('contact_success')
) {
    if ($fields = $session->get('fields')) {
        $template::$fields = $fields;
    }
    if ($invalid_fields = $session->get('invalid_fields')) {
        $template::$invalid_fields = $invalid_fields;
    }
    require JModuleHelper::getLayoutPath($class::get('folder'));    
} elseif ($session->get('contact_success')) {
    require JModuleHelper::getLayoutPath($class::get('folder'), 'thank_you');
    $session->clear('contact_success');
} else {
    if ($template::validateForm()) {
        $template::send();
    }
}
