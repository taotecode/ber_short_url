<?php
/**
 * Index
 * @project ber_Short_Url
 * @copyright
 * @author
 * @version
 * @createTime 16:46
 * @filename Index.php
 * @product_name PhpStorm
 * @link
 * @example
 */

namespace app\user\controller;

use app\common\controller\UserController;

class Index extends UserController
{
    /**
     * 后台主页
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        return $this->fetch('', [
            'user' => session('user'),
        ]);
    }

    /**
     * 后台欢迎页
     * @return string
     * @throws \Exception
     */
    public function welcome()
    {
        return $this->fetch();
    }
}