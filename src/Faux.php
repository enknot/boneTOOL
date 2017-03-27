<?php

namespace bone;

abstract class Faux {

    public $valid_tables = [];
    private $db = null;

    public function __construct(\PDO $conn) {
        $this->db = $conn;
    }

    /**
     * Removes all exisiting data in the database table name $table_name and 
     * replaces the data that is returned by the table named method for that 
     * table.
     * @param type $table_name
     */
    public function reset($table_name, $reset = FALSE) {

        if (is_null($this->db))
            return "<p>Cannot use reset, DB not set.</p>";

        if (!in_array($table_name, $this->valid_tables))
            return "<p>$table_name is not a valid table</p>";
        $data = Faux::data($table_name);
        Faux::truncate($table_name, $reset);
        Faux::replaceInto($table_name, $data);
        
        if(method_exists($this, 'clean'))
            $clean = $this->clean();
        
        if(method_exists($this, 'init'))
            $init = $this->init();
        
        return "<p>Resetting '$table_name' data. Data now relects the following:<pre> " 
                . print_r($data, TRUE) . "</p> \n" . print_pre($clean, 1) . "</p> \n" . print_pre($init, 1);
    }


    protected function clean(){
        return false;
    }

    /**
     * Aquires the data for table named $table_name
     * @param type $table_name
     * @param type $as_objects
     * @return type
     */
    public function data($table_name = NULL, $as_objects = FALSE) {
        if (in_array($table_name, $this->valid_tables)) {
            return ($as_objects) ? Faux::toObject($this->$table_name()) : $this->$table_name();
        } else {
            return "The table method for table '$table_name' is not in the valid tables array";
        }
    }

    /**
     * Notifes programmer when they try to use data from a data method that 
     * was not yet defined.
     * @param type $name
     * @param type $arguments
     * @throws type
     */
    public function __call($name, $arguments) {

        if (is_null($this->db))
            return "<p>Cannot $name reset, DB not set.</p>";

        if (in_array($name, $this->valid_tables)) {
            throw(new \Exception("The table named: '$name' does not have a callable method in this object."));
        }
    }

    /**
     * Places the data from method named $table_name into a table named 
     * $table_name.
     * @param type $table_name
     */
    public function populate($table_name) {
        $data = Faux::data($table_name);
        if (is_array($data)) {
            Faux::replaceInto($table_name, $data);
            return "<p>Populating '$table_name' data. Data now relects the " .
                    "following:<pre> " . print_r($data, TRUE) . '</p>';
        } else {
            return "<p>{$data}</p>";
        }
    }

    /**
     * Adds data to 
     * @param type $table
     * @param array $data
     * @return type
     */
    private function replaceInto($table, Array $data) {



        if (is_null($this->db))
            return "<p>Cannot replaceInto reset, DB not set.</p>";

        $rerr = array();


        $prep = [];

        foreach ($data as $i => $row) {
            $prep = $this->prepSQL($table, $row, $prep);
            $params = paramify($row);
            $prep['stmt']->execute($params);
            $rerr[$i] = $prep['stmt']->errorInfo();
        }

        return $rerr;
    }

    /**
     * Prepares each row of data for insert. Updates params and SQL when needed.
     * @param type $data_row
     * @param type $last_round
     * @return type
     */
    private function prepSQL($table, $data_row, $last_round = []) {

        $ret = [
            'stmt' => null,
            'fields' => []
        ];


        $ret['fields'] = $param_fields = array_keys($data_row);

        if (isset($last_round['fields']) && (count(array_diff($ret['fields'], $last_round['fields'])) == 0)) {
            return $last_round;
        }

        foreach ($ret['fields'] as $key => $field) {
            $ret['fields'][$key] = "`{$field}`";
        }
        $field_list = implode(', ', $ret['fields']);


        foreach ($param_fields as $field) {
            $params[] = ":" . $field;
        }
        $param_list = implode(', ', $params);

        $sql = "REPLACE INTO `{$table}` ({$field_list}) VALUES ({$param_list});";

        $ret['stmt'] = $this->db->prepare($sql);

        return $ret;
    }

    /**
     * Removes all data from $table and optionally resets auto increment to 0.
     * @param type $table
     * @param type $reset
     * @return type
     */
    private function truncate($table, $reset = false) {
        $rerr = array();

        $sql['truncate'] = "TRUNCATE TABLE `{$table}`;";
        $sql['reset'] = "ALTER TABLE `{$table}` AUTO_INCREMENT = 1;";

        if ($this->db->exec($sql['truncate']) == 0) {
            $rerr['truncate'] = $this->db->errorInfo();
        }

        if ($reset) {
            if ($this->db->exec($sql['reset']) == 0) {
                $rerr['reset'] = $this->db->errorInfo();
            }
        }

        return $rerr;
    }

    private function toObject(Array $collection, $obj_name = null, \Models\Collection $collection_name = null) {
        $ret = array();

        foreach ($collection as $index => $arr) {

            if ($obj_name) {
                $ret[$index] = new $obj_name($arr);
            } else {
                $ret[$index] = (object) $arr;
            }
        }

        return $ret;
    }

}
