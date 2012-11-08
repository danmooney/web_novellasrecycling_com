<?php

class ModContact
{
    private   static $folder;
    private   static $db;
    private   static $table;
    private   static $fields;
    private   static $joins;
    private   static $where;
    private   static $order;
    private   static $limit;
    private   static $arr_field_values;
    private   static $main_query;
    private   static $params;
    protected static $results;
    protected static $extra_queries;
    
    /**
     * Map of article id to page type (i.e. Corporate, Residential, etc.)
     * @var array
     */
    public static $page_type_id_map = array(
        5 => 'Corporate'
    );
    
    /**
     * List of states by abbreviation.  Used for HTML selects
     * @var array
     */
    public static $state_list = array(
        '',
        'AL',
        'AK',  
        'AZ',  
        'AR',  
        'CA',  
        'CO',  
        'CT',  
        'DE',  
        'DC',  
        'FL',  
        'GA',  
        'HI',  
        'ID',  
        'IL',  
        'IN',  
        'IA',  
        'KS',  
        'KY',  
        'LA',  
        'ME',  
        'MD',  
        'MA',
        'MI',
        'MN',
        'MS',
        'MO',
        'MT',
        'NE',
        'NV',
        'NH',
        'NJ',
        'NM',
        'NY',
        'NC',
        'ND',
        'OH',
        'OK',
        'OR',
        'PA',
        'RI',
        'SC',
        'SD',
        'TN',
        'TX',
        'UT',
        'VT',
        'VA',
        'WA',
        'WV',
        'WI',
        'WY'
    );
    
    public static function initialize()
    {
        if (isset($db)) {
            return;
        }
        
        self::$folder =  basename(dirname(__FILE__));
        self::$db     =& JFactory::getDBO();
    }
    
    public static function set($field, $value)
    {
        if (is_array(self::$$field)) {
            array_push(self::$$field, $value);  //shorthand throws error
        } else {
            self::$$field = $value;
        }
    }
    
    public static function get($field)
    {
        return self::$$field;
    }
    
    public static function loadAssets()
    {
        $assets_folder =  JPATH_SITE . DS . 'modules' . DS . self::$folder . DS . 'assets';
        if (!file_exists($assets_folder)) {
            return;
        }
        $document      =& JFactory::getDocument();
        $arr_folders   =  array_slice(scandir($assets_folder), 2);
        
        foreach ($arr_folders as $asset_folder) {
            $folder_url = JURI::base() . 'modules' . '/' . self::$folder . '/' . 'assets' . '/' . $asset_folder . '/';
            $arr_files =  array_slice(scandir($assets_folder . DS . $asset_folder), 2);
            foreach ($arr_files as $file) {
                $file_url = $folder_url . $file;
                if ($asset_folder == 'css') {
                    $document->addStyleSheet($file_url);
                } elseif ($asset_folder == 'js') {
                    $document->addScript($file_url);
                }
            }
        }
    }
}