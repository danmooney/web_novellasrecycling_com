<?php

class ModContactTemplate extends ModContact
{
    public static $validators = array (
        'first_name' => 'isNotEmpty',
        'last_name' => 'isNotEmpty',
        'email' => 'isEmail'
    );
    
    public static $fields = array();
    public static $invalid_fields = array();
    
    public static function isNotEmpty($value)
    {
        if (trim($value) === '') {
            return false;
        }
        
        return true;
    }
    
    public static function isEmail($value)
    {
        if (!preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $value)) {
            return false;
        }
        
        return true;
    }
    
    public static function validateForm()
    {
        $post = JRequest::get('post');
        unset($post['option']);
        unset($post['view']);
        
        foreach ($post as $key => $value) {
            if (!isset(self::$validators[$key])) {
                continue;
            }    
            
            $validator_function = (string) self::$validators[$key];
            if (call_user_func('self::' . $validator_function, $value) === false) { // invalid
                self::$invalid_fields[$key] = true;
            }
        }
        
        if (count(self::$invalid_fields) > 0) {
            self::invalidate();
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit();
        }
        
        // everything is valid
        return true;
    }
    
    public static function invalidate()
    {
        $session = JFactory::getSession();
        $session->set('fields', JRequest::get('post'));
        $session->set('invalid_fields', self::$invalid_fields);
    }
    
    public static function send()
    {
        $mod_contact = JModuleHelper::getModule('mod_moo_contact');
        $params  = json_decode($mod_contact->params);
        
        $post = JRequest::get('post');
        unset($post['option']);
        unset($post['view']);
        
        $id = JRequest::getVar('id');
        
        // $post['page_type'] = self::$page_type_id_map[$id];
        
        $body  = '<h1>Contact Submission</h1>';
        $body .= '<table cellpadding="0" cellspacing="5" border="0" align="left" width="500">';

        foreach ($post as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $body .= '<tr><td align="left" width="100" valign="top"><strong>' . ucwords(str_replace('_', ' ', $key)) . '</strong></td><td align="left" valign="top">' . $value . '</td></tr>';
        }
        $body .= '</table>';
        
        $mailer =& JFactory::getMailer();
        $config =& JFactory::getConfig();
        $sender = array( 
            $config->getValue('config.mailfrom'),
            $config->getValue('config.fromname')
        );
        $mailer->setSender($sender);
        $mailer->addRecipient($params->email_to);
        $mailer->setSubject($params->subject);
        $mailer->setBody($body);
        $mailer->isHTML(true);
        $mailer->encoding = 'base64';
        $mailer->send();
        
        $session = JFactory::getSession();
        
        $session->set('contact_success', 1);
        
        require_once(JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . '_moo_component_maker' . DS . 'table.php');
        $contacts_table = new MooTable('#__moo_contact_submission');
        $contacts_table->bind($post);
        $contacts_table->store();

        header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit();
    }
    
    public static function input($name, $additional_style = '', $additional_attributes = '')
    {
        $html = '';
        if (isset(self::$invalid_fields[$name])) {
            $invalid_class = 'class="invalid"';
        }
        if (isset(self::$fields[$name])) {
            $value = self::$fields[$name];
        }
        if ($additional_style) {
            $additional_style = 'style="' . $additional_style . '"';
        }
        
        $html .= '<input ' . @$invalid_class . ' type="text" name="' . $name . '" id="' . $name . '" ' . @$additional_style . ' ' . $additional_attributes . ' value="' . @$value . '" />';
        return $html;
    }
    
    public static function textarea($name, $additional_style = '', $additional_attributes = '')
    {
        $html = '';
        
        if ($additional_style) {
            $additional_style = 'style="' . $additional_style . '"';
        }
        if (isset(self::$invalid_fields[$name])) {
            $invalid_class = 'class="invalid"';
        }
        if (isset(self::$fields[$name])) {
            $value = self::$fields[$name];
        }
        $html .= '<textarea ' . @$invalid_class . ' name="' . $name . '" id="' . $name . '" ' . @$additional_style . ' ' . $additional_attributes . '>' . @$value . '</textarea>';
        return $html;
    }
    
    public static function stateList()
    {
        $name = 'state';
        $options = array();
        foreach (self::$state_list as $state) {
            $options[] = JHTML::_('select.option', $state, $state);         
        }
        
        if (isset(self::$invalid_fields[$name])) {
            $invalid_class = 'class="invalid"';
        }
        if (isset(self::$fields[$name])) {
            $value = self::$fields[$name];
        }
        
        return JHTML::_('select.genericlist', $options, 'state', @$invalid_class, 'value', 'text', @$value);
    }
    
    public static function thankYouMessage()
    {
        $mod_contact = JModuleHelper::getModule('mod_moo_contact');
        $params  = json_decode($mod_contact->params);
        if (!$params->thank_you) {
            return 'Thank you for your submission.  We will get back to you shortly.';
        }
        return $params->thank_you;
    }
    
    public static function invalidationMessage()
    {
        if (!empty(self::$invalid_fields)) {
            return '<div class="invalidate-message general">Please correct the highlighted fields below.</div>';
        }
        return '';
    }
}