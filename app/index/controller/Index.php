<?php
/**
 * Index
 * @project ber_Short_Url
 * @copyright
 * @author
 * @version
 * @createTime 16:39
 * @filename Index.php
 * @product_name PhpStorm
 * @link
 * @example
 */

namespace app\index\controller;

use app\common\controller\IndexController;

class Index extends IndexController
{
    public function index()
    {
        return $this->fetch();
    }

    public function link()
    {
        $link = input('?link');
        echo $link . '<br>';
        $queryData = request()->param();
        print_r($queryData);
        return url('jy');
    }
}