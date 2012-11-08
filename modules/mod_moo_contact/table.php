<?php

class MooTable extends JTable
{
    private $num_primary_keys = 0;
    private $primary_keys = array();
    public function __construct($table = NULL)
    {
        $db = JFactory::getDBO();
        if (is_null($table)) {
            $table = MooHelper::getCurrentTable();
        }
        $query = 'SHOW COLUMNS FROM ' . $table;

        $db->setQuery($query);
        $results = $db->loadObjectList();

        foreach ($results as $result) {
            if ($result->Key === 'PRI') {
                $this->primary_key = $result->Field;
                $this->num_primary_keys += 1;
                $this->primary_keys[] = $this->primary_key;
            }

            $field_name = $result->Field;
            $this->$field_name = NULL;
        }

        if ($this->num_primary_keys > 1) {
            $this->primary_key = join(', ', $this->primary_keys);
        }

        parent::__construct($table, $this->primary_key, $db);
    }

    public function store($updateNulls = false, $insertBool = false)
    {
        unset ($this->primary_key);

        if (true === $insertBool) {
            $k = $this->_tbl_key;
            $pk_val = $this->$k;
            unset ($this->$k);
        }

        // Initialise variables.
        $k = $this->_tbl_key;

        // The asset id field is managed privately by this class.
        if ($this->_trackAssets)
        {
            unset($this->asset_id);
        }

        // If a primary key exists update the object, otherwise insert it.
        if ($this->$k)
        {
            $stored = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
        }
        else
        {
            if (true === $insertBool) {
                $this->$k = $pk_val;
            }
            $stored = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
        }

        // If the store failed return false.
        if (!$stored)
        {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // If the table is not set to track assets return true.
        if (!$this->_trackAssets)
        {
            return true;
        }

        if ($this->_locked)
        {
            $this->_unlock();
        }

        //
        // Asset Tracking
        //

        $parentId = $this->_getAssetParentId();
        $name = $this->_getAssetName();
        $title = $this->_getAssetTitle();

        $asset = JTable::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
        $asset->loadByName($name);

        // Re-inject the asset id.
        $this->asset_id = $asset->id;

        // Check for an error.
        if ($error = $asset->getError())
        {
            $this->setError($error);
            return false;
        }

        // Specify how a new or moved node asset is inserted into the tree.
        if (empty($this->asset_id) || $asset->parent_id != $parentId)
        {
            $asset->setLocation($parentId, 'last-child');
        }

        // Prepare the asset to be stored.
        $asset->parent_id = $parentId;
        $asset->name = $name;
        $asset->title = $title;

        if ($this->_rules instanceof JAccessRules)
        {
            $asset->rules = (string) $this->_rules;
        }

        if (!$asset->check() || !$asset->store($updateNulls))
        {
            $this->setError($asset->getError());
            return false;
        }

        if (empty($this->asset_id))
        {
            // Update the asset_id field in this table.
            $this->asset_id = (int) $asset->id;

            $query = $this->_db->getQuery(true);
            $query->update($this->_db->quoteName($this->_tbl));
            $query->set('asset_id = ' . (int) $this->asset_id);
            $query->where($this->_db->quoteName($k) . ' = ' . (int) $this->$k);
            $this->_db->setQuery($query);

            if (!$this->_db->execute())
            {
                $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));
                $this->setError($e);
                return false;
            }
        }

        return true;
//        return parent::store($updateNulls);
    }

    /**
     * Override the cache - DM - 110712
     * Get the columns from database table.
     *
     * @return  mixed  An array of the field names, or false if an error occurs.
     *
     * @since   11.1
     */
    public function getFields()
    {
        /**
         * Joomla's JTable acting really funky with merging tables with cache being static - DM - 110712
         */
        /*static */$cache = null;

        if ($cache === null)
        {
            // Lookup the fields for this table only once.
            $name = $this->_tbl;

            $fields = $this->_db->getTableColumns($name, false);

            if (empty($fields))
            {
                $e = new JException(JText::_('JLIB_DATABASE_ERROR_COLUMNS_NOT_FOUND'));
                $this->setError($e);
                return false;
            }
            $cache = $fields;
        }

        return $cache;
    }
}