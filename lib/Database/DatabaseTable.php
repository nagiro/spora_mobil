<?php

class DBTable {
    private $name;
    private $primaryKey;
    private $fields;
    private $types;
    private $enumDomains;
    private $sorting;
    private $sortingOrder;

    const SORT_ASC  = 'ASC';
    const SORT_DESC = 'DESC';

    public function  __construct($name, $pkValue = null) {
        $this->name = $name;

        $this->setColumns();

        if($pkValue) {
            $this->read($pkValue);
        }
    }

    private final function __clone() {}

    public function getTableName() {
        return $this->name;
    }

    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    public function store() {
        return ($this->isNewEntry())? $this->insert() : $this->update();
    }

    public function insert() {
        $query = 'INSERT INTO ' . $this->name . ' (' . join(', ', $this->getColumnNames(false) ) . ') VALUES (' . join(', ', $this->getInsertColumns() ) . ')';
        $params = $this->getColumnValues(false);

        $db = Database::getInstance();
                                      
        $newID = $db->insert($query, $params);
        
        if($newID !== false) {
            $this->setFieldValue($this->primaryKey, $newID);

            return true;
        }

        return false;
    }

    public function update() {
        $query = 'UPDATE `' . $this->name . '` SET ' . join(',', $this->getUpdateColumns() ) . ' WHERE `' . $this->primaryKey . '` = :' . $this->primaryKey . ' ';
        $params = $this->getColumnValues();

        $db = Database::getInstance();

        return $db->exec($query, $params);
    }

    public function delete() {
        $pk = $this->primaryKey;
        $pkValue = $this->getFieldValue($pk);
        $db = Database::getInstance();

        $query = sprintf('DELETE FROM `%s` WHERE `%s` = :%s', $this->name, $pk, $pk);
        $params = array(':' . $pk => $pkValue);

        return $db->exec($query, $params);
    }

    public function read($pkValue) {
        $query = 'SELECT ' . join(', ', $this->getColumnNames(true) ) . ' FROM ' . $this->name . ' WHERE ' . $this->primaryKey . '= :' . $this->primaryKey;

        $db = Database::getInstance();

        $fields = $db->query($query, array(':' . $this->primaryKey => $pkValue));

        if(is_array($fields) && isset($fields[0])) {
            $fields = $fields[0];

            foreach($fields as $name => $value) {
                $this->setFieldValue($name, $value);
            }
        }

        return true;
    }

    //Returns an associative array of all entries of a table, using the key =>
    //value pairs determined by the user
    public function readAll() {
        $query = 'SELECT ' . join(', ', $this->getColumnNames(true) ) . ' FROM ' . $this->name;
        $params = array();
        
        if($this->isNewEntry()) {
            $fields = $this->getAllFields();
            $filterStr = array();

            foreach($this->fields as $name => $value) {
                if(!empty($value)) {
                    $filterStr[]= '`' . $name . '` = :' . $name;
                    $params[$name] = $value;
                }
            }

            if(!empty($filterStr)) {
                $query.= ' WHERE ' . join(' AND ', $filterStr);
            }
        }

        if($this->isSortingSet()) {
            $query.= ' ORDER BY `' . $this->getSorting() . '` ' . $this->sortingOrder;
        }
        
        $db = Database::getInstance();

        $fields = $db->query($query, $params);

        return (is_array($fields))? $fields : false;
    }

    public function select() {
        $query = 'SELECT ' . join(', ', $this->getColumnNames(true) ) . ' FROM ' . $this->name;
        $params = array();

        if($this->isNewEntry()) {
            $fields = $this->getAllFields();
            $filterStr = array();

            foreach($this->fields as $name => $value) {
                if(!empty($value)) {
                    $filterStr[]= '`' . $name . '` = :' . $name;
                    $params[$name] = $value;
                }
            }

            if(!empty($filterStr)) {
                $query.= ' WHERE ' . join(' AND ', $filterStr);
            }
        }

        if($this->isSortingSet()) {
            $query.= ' ORDER BY `' . $this->getSorting() . '` ' . $this->sortingOrder;
        }

        $query.= ' LIMIT 1';

        $db = Database::getInstance();

        $fields = $db->query($query, $params);

        if(!is_array($fields) || !isset($fields[0])) {
            return false;
        }

        $fields = $fields[0];

        foreach($fields as $name => $value) {
            $this->fields[$name] = $value;
        }

        return (is_array($fields))? $fields : false;
    }

    //Field methods
    public function getAllFields() {
        return $this->fields;
    }

    public function setFieldValue($name, $value) {
        if($this->isSetField($name)) {
            $this->fields[$name] = $value;
        }
    }

    public function getFieldValue($name) {
        return ($this->isSetField($name))? $this->fields[$name] : null;
    }

    public function getFieldType($name) {
        return ($this->isSetField($name))? $this->types[$name] : null;
    }

    public function isSetField($name) {
        return array_key_exists($name, $this->fields);
    }

    public function isNewEntry() {
        $pk = $this->getFieldValue($this->primaryKey);        
        return empty($pk);
    }

    //SQL syntax generation methods
    private function setColumns() {
        $query = 'SHOW COLUMNS FROM `' . $this->name . '`';

        $db = Database::getInstance();

        $columns = $db->query($query);

        $this->fields = array();
        $this->types = array();
        $this->enumDomains = array();

        if(is_array($columns)) {
            foreach($columns as $column) {
                $name = $column['Field'];
                $type = strtolower($this->getColumnType($column['Type']));

                if($column['Key'] == 'PRI') {
                    $this->primaryKey = $name;
                }

                if($type == 'enum') {
                    $this->setEnumDomain($name, $column['Type']);
                }

                $this->fields[$name] = null;
                $this->types[$name] = $type;
            }
        }
    }

    private function getColumnType($type) {
        if(strpos($type, '(')) {
            $type = substr($type, 0, strpos($type, '('));
        }

        return $type;
    }

    private function getUpdateColumns() {
        $columns = array();

        foreach($this->fields as $name => $value) {
            if($name === $this->primaryKey) {
                continue;
            }
            
            $columns[] = ' `' . $name . '` = :' . $name;
        }

        return $columns;
    }

    private function getInsertColumns() {
        $columns = array();

        foreach($this->fields as $name => $value) {
            if(strcmp($name, $this->primaryKey)) {
                $columns[] = ':' . $name;
            }
        }

        return $columns;
    }

    private function getColumnNames($includePK) {
        $columns = array();

        foreach($this->fields as $name => $value) {
            if($includePK || strcmp($name, $this->primaryKey) != 0) {
                $columns[] = '`' . $name . '`';
            }
        }

        return $columns;
    }

    private function getColumnValues($includePK = true) {
        $columns = array();

        foreach($this->fields as $name => $value) {
            if($includePK || strcmp($name, $this->primaryKey) != 0) {
                $columns[':' . $name] = $value;
            }
        }

        return $columns;
    }

    private function setEnumDomain($name, $typeStr) {
        if(strpos($typeStr, '(') !== false && strpos($typeStr, ')') !== false) {
            $valuesStr = substr($typeStr, strpos($typeStr, '(') + 1);
            $valuesStr = substr($valuesStr, 0, strpos($valuesStr, ')'));
            $valuesStr = str_replace('\'', '', $valuesStr);

            $values = explode(',', $valuesStr);

            if(count($values) > 0) {
                $this->enumDomains[$name] = $values;
                return true;
            }
        }

        return false;
    }

    public function getEnumDomain($name) {
        if(is_array($this->enumDomains) && isset($this->enumDomains[$name])) {
            return $this->enumDomains[$name];
        } else {
            return false;
        }
    }

    public function  __toString() {
        return print_r($this->fields, true);
    }

    public final function  __set($name, $value) {
        if($this->isSetField($name)) {
            $this->setFieldValue($name, $value);
        }
    }

    public final function __get($name) {
        if($this->isSetField($name)) {
            return $this->getFieldValue($name);
        } else {
            return false;
        }
    }

    public function setSorting($field, $order = DBTable::SORT_ASC) {
        if($this->isSetField($field)) {
            $this->sorting = $field;
            $this->sortingOrder = $order;
        }
    }

    public function getSorting() {
        return $this->sorting;
    }

    public function isSortingSet() {
        return ( isset($this->sorting) && !empty($this->sorting) && $this->isSetField($this->sorting) );
    }
}

?>
