<?php

namespace Core;

require_once  'core/Database.php';

class BaseModel extends \Core\Database
{
    protected $table = '';
    protected $primaryId = '';

    private $queryParams = [];

    public function __construct($table, $primaryId = 'id')
    {

        parent::__construct();
        $this->table = $table;
        $this->primaryId = $primaryId;

        $this->initQueryParams();
    }

    /** 
     * @ init structure  queryParams 
     * string select => columns-name
     * string join  =>  tablename
     * params
     */
    private function initQueryParams()
    {
        $this->queryParams = [
            'select' => '*',
            'join' => '',
            'params' => '',
            'where' => null,
            'order by' => '',
            'limit' => '',
            'field' => '',
            'value' => [],
        ];
    }

    /**
     * return a array, all values
     * $queryParams => structor as then $this->queryParams
     */
    public function query($sql)
    {
        //die($sql);
        $result = $this->dbcon->query($sql);
        
        if (!$result && DEBUG_MODE) {
            echo $sql . '<br>';
            die("Error description: " . $this->dbcon -> error);
        }

        return $result;
    }

    public function getByQuery($sql)
    {
        $result = $this->dbcon->query($sql) or die($this->dbcon->errno);
        return $result;
    }

    /**
     * @  $sql: a string
     * @ return $data: array ;
     */
    private function query_array($sql)
    {
        $data = [];
        $query = $this->query($sql);

        if ($query) {
            while ($row = $query->fetch_assoc()) {
                array_push($data, $row);
            }
        }
        return $data;
    }

    public function find($table, $id)
    {
        $sql = "SELECT * FROM $table WHERE $this->primaryId = $id";
        return $this->query($sql)->fetch_assoc();
    }


    /* ======= mit QUERY-PARAMETER */
    /**
     * get all Data
     * string $table : tablename
     */

    public function all($table, $queryParams = [])
    {
        $newParams = array_merge($this->queryParams, $queryParams);
        $sql = $this->buildSQLFromParams($table, $newParams);
        #die($sql);
        $query = $this->query_array($sql);
        return $query;
    }

    public function get($table, $queryParams = [])
    {
        $newParams = array_merge($this->queryParams, $queryParams);
        $sql = $this->buildSQLFromParams($table, $newParams);
        #die($sql);
        $query = $this->query_array($sql);
        return $query;
    }

    /**
     * array input: 
     */
    public function where($table, $col, $operator, $value)
    {
        $query = 'SELECT * FROM ' . $table . ' WHERE ';
        $query .= $col . ' ' . $operator . ' ' .  $this->escape_string($value);
        return $this->query_array($query);
    }

    /**
     * $queryParams    
     */

    public function select_once($table, $queryParams = [])
    {
        return $this->getOnce($table, $queryParams);
    }

    public function getOnce($table, $queryParams = [])
    {
       
        $newParams = array_merge($this->queryParams, $queryParams, ['LIMIT' => '1']);
        $query =  $this->query($this->buildSQLFromParams($table, $newParams));
        if ($query->num_rows > 0) {
            return $query->fetch_assoc() ;
        }
    }

    /**
     * add new item 
     * Array $data =
     *  col => value,
     */

    public function create($table, $data)
    {
        $columns = implode(", ", array_keys($data));
        /*  escape_string */
        $value_array = array_map(function ($value) {
            return $this->escape_string($value);
        }, $data);

        $values = "'" . implode("','", $value_array) . "'";

        $sql  = "INSERT INTO $table  ($columns) VALUES ($values) ";

        $query = $this->query($sql); 

        return $query;
    }

    public function insert($table, $data)
    {
        return $this->create($table, $data);
    }

    public function count($table)
    {
        $query = $this->query("SELECT 1 from $table");
        return $query->num_rows;
    }

    public function update($table, $id, $data)
    {
        $updateValue = $this->convertArrayToSqlPair($data);
        if (count($updateValue) > 0) {
            $dataSetString = implode(', ', $updateValue);
            $sql = "UPDATE $table SET $dataSetString WHERE $this->primaryId = $id ";
            return $this->query($sql);
        }
        return true;
    }


    /* $ids is array  [1, 4, 5 , 6] */
    public function delete_by_ids($ids)
    {
        if (!empty($ids)) {
            $idsStr = implode(', ', $ids);
            $sql = "DELETE FROM $this->table  WHERE $this->primaryId IN ( $idsStr )";
            return $this->query($sql);;
        }
    }

    /* $ids is string '1, 2, 4' */
    public function delete($table, $ids)
    {
        if ($ids != '') {
            $sql = "DELETE FROM $table  WHERE $this->primaryId IN ( $ids )";
            $result = $this->query($sql);
            return $result;
        }
    }

    /**
     * @ input array: key, value, operator  
     */
    public function deleteByWhere($table, $params)
    {
        $Where = isset($params['where']) ? $params['where'] : [];
        if (!empty($Where)) {
            $sql = "DELETE FROM $table  WHERE ";
            $sql .= " (" . join(' and ' , $this->convertArrayToSqlPair($Where)) . ") "; 
            $result = $this->query($sql);
            return $result;
        }
    }

    /** ===============================================
     *  ===========    HELPER-FUNCTIONS   =============
     * =============================================== */

    protected function escape_string($value)
    {
        $value = self::validation($value);
        return mysqli_real_escape_string($this->dbcon, $value);
    }

    private function _debug($arr)
    {
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }

    /** 
     * @ input Array: key => value
     * @ output array key = value
     */ 
    
    private function convertArrayToSqlPair($data) {
        
        $output = [];

        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $value = $this->escape_string($value);
                array_push($output,  "$key = '$value'");
            };
        }
        return $output;   
    }

    /**
     * @ output, example where key= value
     */

    private function decodeParams($params, $key) {
        $sql = "";
        if (isset($params[$key])) { 
            $value = $params[$key];
            if (is_array($value)) {
                $sql = " (" . join(' and ' , $this->convertArrayToSqlPair($value)) . ") "; 
            }
            else 
                $sql = trim($value) ?? '';    
        }
        return  $sql ?  " $key $sql " : ""; 
    }

    private function  buildSQLFromParams($table, $params)
    {
        $sql = 'SELECT ' . $params['select'];
        $sql .= ' FROM ' . $table ?? $this->table;
        $sql .= trim($params['join']) ? ' ' .  $params['join'] : '';

        /* $sql .= trim($params['where']) ? ' WHERE ' .  $params['where'] : ''; */
        $sql .= $this->decodeParams($params, 'where');
        $sql .= trim($params['order by']) ? ' ORDER BY ' .  $params['order by'] : '';
        $sql .= trim($params['limit']) ? ' LIMIT ' .  $params['limit'] : '';
        return $sql;
    }

    public static function validation($data)
    {
        $data = trim($data);
        $data = stripcslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}
