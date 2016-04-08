<?php
/***************************************************************************
 * 
 * Copyright (c) 2014 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/
 
 
 
/**
 * @author lihuipeng(com@baidu.com)
 * @date 2014/12/21 19:08:59
 * @brief 
 *  
 **/

class DataBase
{
    /**
     * @var mysqli
     */
    private $dbcon = null;
    private static $instance=array();
    private $max_retry = 3;
    private $last_db_conf=null;
    private function __construct($db_conf)
    {
        if(is_array($db_conf)&& !empty($db_conf))
            $this->last_db_conf = $db_conf;
        $this->connection($this->last_db_conf);
    }
    private  function  connection($db_conf){
        if($db_conf!==null){
            $this->dbcon = new mysqli($db_conf['host'],$db_conf['user'],$db_conf['password'],$db_conf['db_name'],$db_conf['port']);
        }
        if($this->dbcon->connect_error)
        {
            die('Connect Error (' . $this->dbcon->connect_errno . ') '. $this->dbcon->connect_error);
        }
        $this->dbcon->query("set names 'utf8'");
    }
    /**
     * @param null $db_conf
     * @return DataBase
     */
    public static function getInstance($db_conf = null)
    {
        if($db_conf === null)
            $db_key = "abc123";
        else
            $db_key = md5(json_encode($db_conf));
        if(isset($instance[$db_key]))
            return self::$instance[$db_key];
        self::$instance[$db_key] = new DataBase($db_conf);
        return self::$instance[$db_key];
    }
    public function getDBCon()
    {
        return $this;
    }
    public function query($str)
    {
        $results = $this->dbcon->query($str);
        //$this->dbcon-

        if($results===false){
            if($this->dbcon->errno == 2006){
                for($i = 0 ; $i< $this->max_retry; $i++){
                    $this->dbcon->close();
                    $this->connection($this->last_db_conf);
                    print_r($this->last_db_conf);
                    $results = $this->dbcon->query($str);
                    if($results!==false){

                        return $results;
                    }
                    echo "Query Error (' . {$this->dbcon->errno} . ') '. {$this->dbcon->error}.\n";
                }
            }
            throw new Exception('Query Error (' . $this->dbcon->errno . ') '. $this->dbcon->error." with sql is ==>".$str);
        }
        return $results;
    }

    public function queryAssocArray($str){
        $output = array();
        $result = $this->query($str);
        if ($result === false) {
            return false;
        }
        while($row = $result->fetch_assoc()){
            $output[] = $row;
        }
        $result->free();
        return $output;
    }

    /**
     * getAffectedRows
     * @param
     * @return
     */
    public function getAffectedRows() {
        return $this->dbcon->affected_rows;
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
