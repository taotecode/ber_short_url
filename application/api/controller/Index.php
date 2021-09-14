<?php
//首页接口

namespace app\api\controller;

use think\Controller;
use think\Debug;
use think\Request;
use think\Cookie;
use think\Session;
use think\Db;
use think\Cache;

class Index extends Common
{
    protected $user_id = null;


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @example 首页生成短链
     */
    public function index()
    {
        $request = Request::instance();
        //检测是否为前端AJAX请求
        if (!$request->isAjax())
            $this->error('非法请求！', '/');
        //检测是否为POST请求
        if (!$request->isPost())
            $this->error('非法请求！', '/');
        //获取请求参数
        $post = array(
            'geetest_challenge' => input('post.geetest_challenge'),
            'geetest_validate' => input('post.geetest_validate'),
            'geetest_seccode' => input('post.geetest_seccode'),
            'mode' => input('post.mode'),
            'url' => input('post.url')
        );
        $ip = Request::instance()->ip();
        //验证滑块
        if (!geetest_check($post)) {
            return ['code' => 600, 'msg' => '安全环境不通过！'];
        }
        if (empty($post))
            return ['code' => '500', 'msg' => '请输入要缩短的长链接'];
        //检测是否为URL
        if (!check_url($post['url']))
            return ['code' => '500', 'msg' => '请输入正确的URL，并包含http://或https://'];
        //指定时间内无法继续生成，需要继续就得登录
        if (Cache::get('url_check_' . $ip))
            return ['code' => 500, 'msg' => '您已经生成过一次了，请过段时间再来。'];
        //检测是否登录
        if (Session::has('name', 'user')) {
            if (db('user')->where('user', Session::get('name', 'user'))->find()) {
                $this->user_id = Session::get('name', 'user');
            }
        }
        //检查IP黑名单
        if (db('black')->where('ip', get_ip())->find())
            return ['code' => 500, 'msg' => '您的IP已被管理员拉黑！'];
        //检查URL黑名单
        if (db('black')->where('url', $post['url'])->find())
            return ['code' => 500, 'msg' => '您要生成的长链已被管理员拉黑！'];
        //分割URL编码
        $url_coded = parse_url($post['url'])['path'];//分割URL参数
        $url_coded=str_replace("/","",$url_coded);
        //URL转码
        $post['url'] = is_url_encoded($post['url']);
        switch ($post['mode']) {
            case 'index-create':
                //生成普通型短链
                Cache::set('url_check' . $ip, '0', $this->config['time_create']);
                return $this->index_sql($post['url'], $this->user_id, 0, '普通型短链');
                break;

            case 'index-anti':
                //生成防红型短链
                Cache::set('url_check' . $ip, '0', $this->config['time_create']);
                return $this->index_sql($post['url'], $this->user_id, 1, '防红型短链');
                break;

            case 'index-reduction':
                //还原短链
                return $this->index_reduction($url_coded,$this->user_id);
                break;

            default:
                return ['code' => 600, 'msg' => '非法请求'];
                break;
        }
    }

    private function index_reduction($url, $user = null)
    {
        $sql_url_check = db('url')->where('coded', $url)->find();
        $sql_user_url_check = db('user_url')->where('coded', $url)->find();
        $sql_user_work_url_check = db('user_url')->where('coded', $url)->find();

        $http_url = 'https://' . $_SERVER['SERVER_NAME'] . '/';

        $type_array = json_decode($this->config['url_type'],true);
        if ($sql_url_check) {//存在，返回数据
            return ['code' => 200, 'url' => $sql_url_check['url'], 'type' => $type_array[$sql_url_check['type']], 'expire' => $sql_url_check['expire_time']];
        } elseif ($sql_user_url_check) {//用户表存在
            return ['code' => 200, 'url' => $sql_user_url_check['url'], 'type' => $type_array[$sql_user_url_check['type']], 'expire' => $sql_url_check['expire_time']];
        } elseif ($sql_user_work_url_check) {//业务表存在
            return ['code' => 200, 'url' => $sql_user_work_url_check['url'], 'type' => $type_array[$sql_user_url_check['type']], 'expire' => $sql_url_check['expire_time']];
        }
    }

    /**
     * @example 首页生成短链\转链
     * @param $url string 长链
     * @param $user int 用户账号
     * @param $type int 类型
     * @param $type_tips string 类型提示
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    private function index_sql($url, $user, $type, $type_tips)
    {
        //提前获取这个URL是否存在数据库，存在就进行操作
        $sql_url_check = db('url')->where('url', $url)->find();
        $sql_user_url_check = db('user_url')->where('url', $url)->find();
        //当前时间戳
        $date_time = time();
        //设置未登录用户短链有效期时间
        $date_add_time = date("Y-m-d H:i:s", strtotime("+" . $this->config['url_expire_time'] . " day"));
        //本网站URL
        $http_url = 'https://' . $_SERVER['SERVER_NAME'] . '/';

        //用户未登录
        if (empty($user))
           $this->index_sql_tourist($sql_url_check,$date_time,$date_add_time,$url,$http_url,$type,$type_tips);
        //用户已登录
        else
            $this->index_sql_user($sql_url_check,$sql_user_url_check,$http_url,$url,$user,$type,$type_tips);
    }


    /**
     * @example 路人生成链接操作数据库
     * @param $sql_url_check
     * @param $date_time
     * @param $date_add_time
     * @param $url
     * @param $http_url
     * @param $type
     * @param $type_tips
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function index_sql_tourist($sql_url_check,$date_time,$date_add_time,$url,$http_url,$type,$type_tips){
        //存在数据库-路人数据库
        if ($sql_url_check) {
            //快要到期，重新存为临时120天
            if (strtotime($sql_url_check['expire_time']) < $date_time) {
                //修改类型和时间
                db('url')->where('url', $url)->update(['type' => $type, 'expire_time' => $date_add_time]);
                return ['code' => 200, 'url' => $http_url . $sql_url_check['coded'], 'type' => $type_tips, 'expire' => $date_add_time];
            }
            //没到期，继续返回剩余天数
            return ['code' => 200, 'url' => $http_url . $sql_url_check['coded'], 'type' => $type_tips, 'expire' => $sql_url_check['expire_time']];
        }
        //未在数据库-直接添加到路人数据库
        //生成短链编码
        $coded = url_coded_short($url);
        //添加数据库
        $sql_table = ['coded' => $coded, 'user' => null, 'ip' => get_ip(), 'url' => $url, 'type' => $type, 'expire_time' => $date_add_time];
        db('url')->insert($sql_table);
        return ['code' => 200, 'url' => $http_url . $coded, 'type' => $type_tips, 'expire' => $date_add_time];
    }

    /**
     * @param $sql_url_check
     * @param $sql_user_url_check
     * @param $http_url
     * @param $url
     * @param $user
     * @param $type
     * @param $type_tips
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function index_sql_user($sql_url_check,$sql_user_url_check,$http_url,$url,$user,$type,$type_tips){
        //存在路人数据库
        if ($sql_user_url_check) {
            //存在用户数据库-修改属性，直接返回
            if ($sql_user_url_check) {
                //先删除路人数据库的数据
                db('url')->where('coded', $sql_url_check['coded'])->delete();
                //修改属性
                db('user_url')->where('coded', $sql_user_url_check['coded'])->update(['type' => $type]);
                return ['code' => 200, 'url' => $http_url . $sql_user_url_check['coded'], 'type' => $type_tips, 'expire' => '已为用户自动转链，无到期时间'];
            }
            //不存在用户数据库，转链
            //因为是无判断，所以不担心生成重复
            $sql_table = ['coded' => $sql_url_check['coded'], 'user' => $user, 'ip' => get_ip(), 'type' => $type];
            db('user_url')->insert($sql_table);
            //删除路人数据库的数据
            db('url')->where('coded', $sql_url_check['coded'])->delete();
            return ['code' => 200, 'url' => $http_url . $sql_url_check['coded'], 'type' => $type_tips, 'expire' => '已为用户自动转链，无到期时间'];
        }
        //存在用户数据库-修改属性，直接返回
        elseif ($sql_user_url_check) {
            //修改属性
            db('user_url')->where('coded', $sql_user_url_check['coded'])->update(['type' => $type]);
            return ['code' => 200, 'url' => $http_url . $sql_user_url_check['coded'], 'type' => $type_tips, 'expire' => '已为用户自动转链，无到期时间'];
        }
        //未在数据库-直接添加到用户数据库
        $coded = url_coded_short($url,$user);
        //因为是无判断，所以不担心生成重复
        $sql_table = ['coded' => $coded, 'user' => $user, 'ip' => get_ip(), 'url' => $url, 'type' => $type];
        db('user_url')->insert($sql_table);
        return ['code' => 200, 'url' => $http_url . $coded, 'type' => $type_tips, 'expire' => '已为用户自动转链，无到期时间'];
    }
}