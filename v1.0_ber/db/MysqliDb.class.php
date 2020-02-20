<?php
/** 
* Mysqli 连接数据库类
* author Lee. 
* Last modify $Date: 2012-11-30 $ 
*/
class MysqliDb {
    //获取对象句柄
    static public function getDB() {
        $_mysqli = @new mysqli(DB_HOST, DB_USER, DB_PWD, DB_NAME, DB_PORT);
        if (mysqli_connect_errno()) {
                echo '数据库连接错误！错误代码：'.mysqli_connect_error();
                exit();
        }
        $_mysqli->set_charset('utf8');
        return $_mysqli;
    }

    //清理，释放资源
    static public function unDB(&$_result, &$_db) {
        if (is_object($_result)) {
                $_result->free();
                $_result = null;
        }
        if (is_object($_db)) {
                $_db->close();
                $_db = null;
        }
    }

}
?>