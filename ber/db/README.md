1. M类库中的方法参数说明，请到 M.class.php 文件中看详细的注释，这里不再进行叙述。建议在学习的时候，对照着看下文件中的参数即注释。
2. 讲解代码中用到的数据库结构为：
```
CREATE TABLE `user` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `email` varchar(100) default NULL,
  `age` smallint(3) default NULL,
  `class_id` int(8) default NULL,
  `commit_time` int(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
CREATE TABLE `class` (
  `class_id` int(8) NOT NULL auto_increment,
  `class_name` varchar(100) default NULL,
  PRIMARY KEY  (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
```
3. M类库中，大部分方法都分两中类型，即：SQL方法；拼接方法，具体在实例中可以看出
4. 以下称述中的 M 为 M.class.php 文件

- 方法1、Insert() 添加数据 
```
$M->Insert("user", null, array('焦焦', 'liruxing1715@sina.com', '23', time())); // 拼接方法：往`user`表中添加一条数据，返回值为数据库影响的行数
$M->Insert("user", null, array('焦焦', 'liruxing1715@sina.com', '23', time()), true); // 功能同上，返回 last_insert_id（插入的增长id）
$M->Insert("INSERT INTO `user` (`name`, `email`, `age`, `commit_time`) VALUES ('张小花', 'zhangxiaohua@sina.com.cn', '22', '".time()."')"); //SQL方法，返回值为数据库影响的行数
$M->Insert("INSERT INTO `user` (`name`, `email`, `age`, `commit_time`) VALUES ('张小花', 'zhangxiaohua@sina.com.cn', '22', '".time()."')", true); // 同上，返回 last_insert_id
```
注：Insert 方法中的第二个参数若为null，可自动获得插入表除 auto_increment 字段之外的所有字段，详情请看M源文件；若要返回值为最后插入的 ID，那么请设置 Insert 方法的最后一个参数为 true（默认是false）;
- 方法2、Update() 修改数据
```
$M->Update("user", array('name'=>'李茹茹', 'age'=>24), "id=1"); // 拼接方法，修改id为1的数据的名称为“李茹茹”；年龄为“24”，其方法的返回值为受影响的行数
$M->Update("UPDATE `user` SET `name`='李茹茹', `age`=24 WHERE id = 1"); //SQL 用法，功能同上
```
- 方法3、Del() 删除数据 
```
$M->Del('user', 'id=3'); //拼接方法：删除`user`表中 id 为3的数据，返回受影响的行数
$M->Del("DELETE FROM `user` WHERE id=4"); //SQL方法：删除`user`表中 id 为4的数据，返回受影响的行数
$M->Del("DELETE FROM `user` WHERE id in (10, 11, 12)"); //SQL方法：删除多条数据，删除`user`表中 id 为 10、11、12 的数据，返回受影响的行数
```
- 方法4、Total() 获取记录数，返回值都为int
```
$M->Total('user'); //拼接方法：返回 `user`表中的记录数，无条件
$M->Total('user', 'id>1'); //拼接方法：返回 `user`表中 id 大于1的记录数，有条件
$M->Total("SELECT COUNT(*) AS total FROM `user`"); //SQL方法，注：使用SQL方法，语句中必须使用 "AS total"，否则会报错
```
- 方法5、IsExists() 检查数据是否存在，返回值为boolean
```
$M->IsExists('user', "`name`='焦焦'");  //拼接方法：返回`user`表中是否存在`name`为“焦焦”的数据，返回true，若不存在，返回false
```
- 方法6、InsertId() 获取表下一个添加的自动增长id，注意，这里不进行添加操作，只是获取下一个增长id 
```
echo $M->InsertId('user'); //获取`user` 表下一个添加的自动增长id
```
- 方法7、GetRow() 返回单条数据，返回值为一维数组 
```
$data = $M->GetRow("SELECT `name`,email FROM `user` WHERE id=1");  //SQL方法，返回一维数组，例如：Array ( [name] => 焦焦 [email] => liruxing1715@sina.com ) 
$data = $M->GetRow("SELECT u.`name`, u.email, c.class_name FROM `user` u, `class` c WHERE u.class_id=c.class_id AND u.id=1");  //SQL方法，多表查询
$data = $M->GetRow('user', '`name`,email', "id=1");  //拼接方法
$data = $M->GetRow('user as u,`class` c', 'u.`name`,u.email,c.class_name', "u.id=1 AND u.class_id=c.class_id"); //拼接方法，多表查询
$data = $M->GetRow("SELECT `name`,email FROM `user`"); //如果没有指定条件应该是显示全部信息，但是在此方法中将默认显示第一条（不推荐这么使用！！！）
```
$data 是查询出来的一维数组。 

- 方法8、GetOne() 返回单个数据
```
$name = $M->GetOne("SELECT `name` FROM `user` WHERE id=1");  //SQL方法，返回一个字符串，例如：焦焦
$name = $M->GetOne("user", "name", "id=1");  //拼接方法，返回一个字符串，例如：焦焦
```
- 方法9、FetchAll() 返回所有记录 
```
$data = $M->FetchAll("user");  //返回`user`表中的所有记录，以二维数组的形式
$data = $M->FetchAll("SELECT * FROM `user`");  //SQL 方法，功能和返回值同上
$data = $M->FetchAll("user", "name,email", "id>1", 'id DESC', '2'); //返回两条id>1的数据，只显示name,email，并且以id 为倒序排序。注：请注意该方法的最后一个参数也可以为'0,2'，目的是为分页准备的，如果第一页为'0,2'的话，那么第二页就是'2,2'
//该方法也支持联表查询和多表查询，下面以联表查询为例
$data = $M->FetchAll("`user` as u LEFT JOIN `class` as c ON u.class_id=c.class_id", "u.`name`,u.email, c.class_name", "u.id=1"); //注意：该拼接方法中，ON 添加的位置
```
注：对于该 FetchAll 方法，后续我会写一篇使用该方法进行完美分页的文章！！请关注。 

- 方法10、MultiQuery() 执行多条SQL语句
```
$sql = "INSERT INTO user (`name`,email, age, class_id, commit_time) VALUES ('贾花花', 'jiahuahua@sina.com.cn', '22', '1', '".time()."')"; //添加一个名叫“贾花花”的学生信息
$sql .= ";DELETE FROM `user` WHERE `name`='焦焦'"; //删除一条名叫“焦焦”的学生信息
//解释：$sql 是多条 SQL 以英文;（分号）拼接起来的
$data = $M->MultiQuery($sql); //返回为true，代表执行成功；为false，代表执行失败
```

类库讲解完毕 
到此该类库的全部功能就讲解完毕，希望你能多看看M文件，了解其内部运行的机制。M 文件不会存在执行缓慢情况，请大家放心使用。
如果在使用过程中出现 SQL 拼接错误，类库会报出友善的错误提示。