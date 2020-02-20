<?php  
/** 
* 基于 Mysqli 的数据库操作类库 
* author Lee. 
* Last modify $Date: 2012-11-30 $ 
*/
class  M {
    /** 
     * 数据库添加操作 
     * @param string $tName 表名 || SQL语句
     * @param array $field 字段数组 || 是否返回last_insert_id。若为字段数组，如果为null，可自动获得插入表除 auto_increment 字段之外的所有字段，详情请查看 GetFieldsOfDefault() 方法；若为是否返回last_insert_id，应该传递 boolean 值，如果为 true，那么返回 last_insert_id，如果为 false 或 null，那么返回插入影响的行数。
     * @param array $val 值数组 
     * @param bool $is_lastInsertId 是否返回添加ID 
     * @return int 默认返回成功与否，$is_lastInsertId 为true，返回添加ID 
     */  
    public function Insert($tName, $field=null, $val=array(), $is_lastInsertId=FALSE) {
        $args_count = count(func_get_args());
        $db = MysqliDb::getDB();
        if ($args_count==1 || $args_count==2) {
            if (!is_string($tName) && !is_bool($field)) exit($this->getError(__FUNCTION__, __LINE__));
            $db->query($tName);
            $this->printSQLError($db);
            if ($field) {
                return $db->insert_id;
            } else {
                return $db->affected_rows;
            }
        } else {
            if (!is_string($tName) || (!is_array($field) && !is_null($field)) || !is_array($val) || !is_bool($is_lastInsertId)) exit($this->getError(__FUNCTION__, __LINE__));
            if (is_null($field)) {
                $field = $this->GetFieldsOfDefault($tName);
            } else {
                $field = $this->formatArr($field);
            }
            $val = $this->formatArr($val, false);
            $sql = "INSERT INTO `{$tName}` ({$field}) VALUES ({$val})";
            $db->query($sql);
            $this->printSQLError($db);
            if ($is_lastInsertId) {
                return $db->insert_id;
            } else {
                return $db->affected_rows;
            }
        }
        MysqliDb::unDB(null, $db);
    }
    
    /** 
     * 数据库修改操作 
     * @param string $tName 表名 || SQL 语句
     * @param array $field 字段数组
     * @param string $condition 条件 
     * @return int 受影响的行数 
     */  
    public function Update($tName, $fieldVal=null, $condition=null) {
        $args_count = count(func_get_args());
        $db = MysqliDb::getDB();
        switch ($args_count) {
            case 1:
                if (!is_string($tName)) exit($this->getError(__FUNCTION__, __LINE__));
                $sql = $tName;
                break;
            case 3:
                if (!is_array($fieldVal) || !is_string($tName) || !is_string($condition)) exit($this->getError(__FUNCTION__, __LINE__));
                foreach ($fieldVal as $k=>$v) {
                    $upStr .= $k . '=' . '\'' . $v . '\'' . ',';
                }
                $upStr = rtrim($upStr, ',');
                $sql = "UPDATE {$tName} SET {$upStr} WHERE {$condition}";
                break;
            default:
                exit($this->getError(__FUNCTION__, __LINE__));
        }
        $db->query($sql);
        $this->printSQLError($db);
        return $db->affected_rows;
        MysqliDb::unDB(null, $db);
    }
    
    /** 
     * 数据库删除操作（注：若拼接方法，必须添加 where 条件）
     * @param string $tName 表名 || SQL 语句
     * @param string $condition 条件
     * @return int 受影响的行数
     */
    public function Del($tName, $condition=null) {
        $args_count = count(func_get_args());
        $db = MysqliDb::getDB();
        switch ($args_count) {
            case 1:
                if (!is_string($tName)) exit($this->getError(__FUNCTION__, __LINE__));
                $sql = $tName;
                break;
            case 2:
                if (!is_string($tName) || !is_string($condition)) exit($this->getError(__FUNCTION__, __LINE__));
                $sql = "DELETE FROM {$tName} WHERE {$condition}";
                break;
        }
        $db->query($sql);
        $this->printSQLError($db);
        return $db->affected_rows;
        MysqliDb::unDB(null, $db);
    }

    /** 
     * 返回表总个数 
     * @param string $tName 表名||SQL语句
     * @param string $condition 条件
     * @return int 
     */  
    public function Total($tName, $condition='') {
        $param_array = func_get_args();
        $param_num = count($param_array);
        switch ($param_num) {
            case 1:
                if (!is_string($tName)) exit($this->getError(__FUNCTION__, __LINE__));
                if (substr_count($tName, ' ')) {
                    //SQL语句
                    if (preg_match('/\s+as\s+total\s+/Usi', $tName, $arr)) {
                        $sql = $tName;
                    } else {
                        exit($this->getError(__FUNCTION__, __LINE__, 'SQL must \'as total\''));
                    }
                    $sql = $tName;
                } else {
                    $sql = "SELECT COUNT(*) as total FROM {$tName}";
                }
                break;
            case 2:
                if (!is_string($tName) || !is_string($condition)) exit($this->getError(__FUNCTION__, __LINE__));
                $sql = "SELECT COUNT(*) as total FROM {$tName} WHERE " . $condition;
                break;
            default:
                exit($this->getError(__FUNCTION__, __LINE__));
                break;
        }
        if (!is_string($tName)) exit($this->getError(__FUNCTION__, __LINE__));
        $db = MysqliDb::getDB();
        $result = $this->GetRow($sql);
        $this->printSQLError($db);
        return $result['total'];
        MysqliDb::unDB($result, $db);
    }

    /** 
     * 检查数据是否存在
     * @param string $tName 表名 || SQL 语句
     * @param string $condition 条件 
     * @return bool 有返回 true,没有返回 false
     */  
    public function IsExists($tName, $condition) {
        if (!is_string($tName) || !is_string($condition)) exit($this->getError(__FUNCTION__, __LINE__));
        if ($this->Total($tName, $condition)) {
            return true;
        } else {
            return false;
        }
     }

    /** 
     * 返回添加主键ID 
     * @param string $table 表名
     * @return int 
     */ 
    public function InsertId($table) {
        if (!is_string($table)) exit($this->getError(__FUNCTION__, __LINE__));
        $sql = "SHOW TABLE STATUS LIKE '{$table}'";
        $object = $this->ExecSql($sql);
        $obj = $object->fetch_object();
        return $obj->Auto_increment;
    }

    /**
     * 返回单条数据
     * @param string $tName 表名，如果参数只有一个的话，则为sql语句
     * @param string $condition 条件
     * @param string $fields 返回的字段，默认是*
     * @return Array
     */
    public function GetRow($tName, $fields="*", $condition='') {
        $param_array = func_get_args();
        $param_num = count($param_array);
        switch ($param_num) {
            case 1:
                if (!is_string($tName)) exit($this->getError(__FUNCTION__, __LINE__));
                $sql = $tName;
                break;
            case 3:
                if (!is_string($tName) || !is_string($condition) || !is_string($fields)) exit($this->getError(__FUNCTION__, __LINE__));
                $sql = "SELECT {$fields} FROM {$tName} WHERE {$condition} LIMIT 1";
                break;
            default:
                exit($this->getError(__FUNCTION__, __LINE__));
                break;
        }
        $db = MysqliDb::getDB();
        $result = $db->query($sql);
        $this->printSQLError($db);
        return $result->fetch_assoc();
        MysqliDb::unDB($result, $db);
    }

    /**
     * 返回单个数据
     * @param string $t 表名/SQL语句
     * @param string $condition 条件
     * @param string $field 返回的字段，默认是null
     * @return string
     */
    public function GetOne($t, $field=null, $condition=null) {
        $param_array = func_get_args();
        $param_num = count($param_array);
        $data = null;
        $alert_info = "SQL语句中只能有一个字段，请检查！";
        if ($param_num==1) {
            //走 SQL 语句
            $db = MysqliDb::getDB();
            $result = $db->query($param_array[0]);
            $this->printSQLError($db);
            $field_obj = $result->fetch_fields();
            if (count($field_obj)>1) {
                die($alert_info);
            } else {
                $f = $field_obj[0]->name;
                while (!!$row=$result->fetch_assoc()) {
                    $data = $row[$f];
                }
            }
            MysqliDb::unDB($result, $db);
            return $data;
        } elseif ($param_num==3) {
            //走拼接 SQL
            if (substr_count($field, ',')) die($alert_info);
            $db = MysqliDb::getDB();
            $sql = "SELECT `{$field}` FROM `{$t}` WHERE {$condition}";
            $result = $db->query($sql);
            $this->printSQLError($db);
            while (!!$row=$result->fetch_assoc()) {
                $r = $row;
            }
            foreach ($r as $v) {
                $data = $v;
            }
            MysqliDb::unDB($result, $db);
            return $data;
        } else {
            exit($this->getError(__FUNCTION__, __LINE__));
        }
    }

    /**
     * 返回全部数据，返回值为二维数组
     * @param string $tName 表名 || SQL语句
     * @param string $fields 返回字段，默认为*
     * @param string $condition 条件
     * @param string $order 排序
     * @param string $limit 显示个数
     * @return ArrayObject 
     */
    public function FetchAll($tName, $fields='*', $condition='', $order='', $limit='') {
        $param_array = func_get_args();
        $param_num = count($param_array);
        $space_count = substr_count($tName, ' ');
        $sql = '';
        if ($param_num==1 && $space_count>0) {
            $sql = $tName;
        } else {
            if (!is_string($tName) || !is_string($fields) || !is_string($condition) || !is_string($order) || (!is_string($limit) && !is_int($limit))) exit($this->getError(__FUNCTION__, __LINE__));
            $fields = ($fields=='*' || $fields=='') ? '*' : $fields;
            $condition = $condition=='' ? '' : " WHERE ". $condition ;
            $order = empty($order) ? '' : " ORDER BY ". $order;
            $limit = empty($limit) ? '' : " LIMIT ". $limit;
            $sql = "SELECT {$fields} FROM {$tName} {$condition} {$order} {$limit}";
        }
        $db = MysqliDb::getDB();
        if (empty($sql)) exit($this->getError(__FUNCTION__, __LINE__));
        $result = $db->query($sql);
        $this->printSQLError($db);
        $obj = array();
        while (!!$objects = $result->fetch_assoc()) {
            $obj[] = $objects;
        }
        return $obj;
        MysqliDb::unDB($result, $db);
    }

    /**
     * 执行多条 SQL 语句
     * @param string $sql SQL语句
     * @return bool
     */
    public function MultiQuery($sql) {
        if (!is_string($sql)) exit($this->getError(__FUNCTION__, __LINE__));
        $db = MysqliDb::getDB();
        $bool = $db->multi_query($sql);
        $this->printSQLError($db);
        return $bool;
        MysqliDb::unDB(null, $db);
    }

    /**
     * 打印可能出现的 SQL 错误
     * @param Object $db 数据库对象句柄
     */
    private function printSQLError($db) {
        if ($db->errno) {
            exit("警告：SQL语句有误<br />错误代码：<font color='red'>{$db->errno}</font>；<br /> 错误信息：<font color='red'>{$db->error}</font>");
        }
    }
    
    /** 
     * 格式化数组（表结构和值） 
     * @param array $field 
     * @param bool $isField 
     * @return string 
     */  
    private function formatArr($field, $isField=TRUE) {
        if (!is_array($field)) exit($this->getError(__FUNCTION__, __LINE__));
        if ($isField) {
            foreach ($field as $v) {
                $fields .= '`'.$v.'`,';
            }
        } else {
            foreach ($field as $v) {
                $fields .= '\''.$v.'\''.',';
            }
        }  
        $fields = rtrim($fields, ',');
        return $fields;
    }

    /** 
     * 添加返回默认表字段
     * @param string $tName 
     * @return string 
     */
    public function GetFieldsOfDefault($tName) {
        $field_result = $this->ExecSql("DESC `{$tName}`");
        $field_array = array();
        $field_str = '';
        while (!!$row=$field_result->fetch_assoc()) {
            $field_array[] = $row;
        }
        foreach ($field_array as $val) {
            if (!($val['Null']=='NO' && $val['Extra']=='auto_increment')) {
                $field_str .= "`{$val['Field']}`,";
            }
        }
        $field_str = rtrim($field_str, ',');
        return $field_str;
    }

    /** 
     * 错误提示 
     * @param string $fun 
     * @return string 
     */  
    private function getError($fun, $line, $other="") {  
        return __CLASS__ . '->' . $fun . '() line<font color="red">'. $line .'</font> ERROR! ' . $other;
    }
}
?>