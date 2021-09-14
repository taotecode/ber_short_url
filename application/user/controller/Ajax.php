<?php


namespace app\user\controller;


use Exception;
use think\Cache;
use think\Controller;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Debug;
use think\exception\DbException;
use think\Request;
use think\Session;
use app\user\model\Userurl;

class Ajax extends Controller
{
    protected $user_is_login;
    protected $user_data;
    protected $config;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if (Session::has('name', 'user')) {
            $this->user_data = db('user')->where('user', Session::get('name', 'user'))->find();
            if ($this->user_data) {
                $this->user_is_login = true;
            }
        } else {
            $this->user_is_login = false;
            return ['code' => 00000, 'msg' => '登录信息已失效！请重新登录重试'];
        }
        $this->config = Cache::get('web_config');
        if (empty($this->web_config)) {
            $web_config_data = $this->get_web_config();
            Cache::set('web_config', $web_config_data, '2592000');
            $this->config = $web_config_data;//重新设置缓存有效期30天
        }
    }

    private function get_web_config()
    {
        $config_data = Db::name('config')->column('k');
        foreach ($config_data as $k) {
            $data[$k] = Db::name('config')->where('k', $k)->value('v');
        }
        return $data;
    }

    public function new_url()
    {
        if (!Request::instance()->isPost())
            return ['code' => 50000, 'msg' => '非法请求,no post'];
        if (!Request::instance()->isAjax())
            return ['code' => 50000, 'msg' => '非法请求,no ajax'];
        $post_data = Request::instance()->post()['data'];
        //url配置
        $url_config = array();

        if (empty($post_data['url']))
            return ['code' => 400, 'msg' => '请输入需要生成的长链接'];

        //开始判断第三方短链
        if (!array_key_exists($post_data['domain'], json_decode($this->config['domain_type'], true)))
            return ['code' => 400, 'msg' => '请输入选择正确的短链域名'];

        if ($post_data['domain'] == '1') {//suo.im
            if (empty($this->user_data['suo_key']) || empty($this->user_data['suo_time']))
                return ['code' => 500, 'msg' => '请先到[链接配置]配置suo.im生成选项'];
        } elseif ($post_data['domain'] == '2') {
            if ($this->user_data['w_url'] < 0)
                return ['code' => 500, 'msg' => '您的w.url.cn生成次数已空，请充值'];
        } elseif ($post_data['domain'] == '3') {
            if ($this->user_data['vip_type'] < 2)
                return ['code' => 500, 'msg' => '非会员不可使用[app.10086.cn]'];
        } else {
            //使用其他
            $domain_type = json_decode($this->config['domain_type'], true);
            $http_url = $this->config['Response_protocol'] . $domain_type[$post_data['domain']] . '/';
        }

        if (!array_key_exists($post_data['type'], json_decode($this->config['url_type'], true)))
            return ['code' => 400, 'msg' => '请输入选择正确的短链属性'];

        //判断是否存在预览标题与小标题
        if (in_array($post_data['type'], [3, 4, 5])) {
            //不存在则使用用户配置的
            if (empty($post_data['plain_title']))
                $post_data['plain_title'] = $this->user_data['plain_title'];
            //存在就添加到URL配置
            else{
                $url_config['plain_title_on']='true';
                $url_config['plain_title']=$post_data['plain_title'];
            }

            //不存在则使用用户配置的
            if (empty($post_data['plain_text']))
                $post_data['plain_text'] = $this->user_data['plain_text'];
            //存在就添加到URL配置
            else{
                $url_config['plain_text_on']='true';
                $url_config['plain_text']=$post_data['plain_text'];
            }
        }

        //判断是否存在防红页面提示
        if ($post_data['type'] == '2') {
            //不存在则使用用户配置的
            if (empty($post_data['html_tips']))
                $post_data['html_tips'] = $this->user_data['html_tips'];
            //存在就添加到URL配置
            else{
                $url_config['html_tips_on']='true';
                $url_config['html_tips']=$post_data['html_tips'];
            }
        }

        //密码访问功能-如果关闭
        if (empty($post_data['pass_switch'])) {
            $post_data['fw_pass'] = null;
        } else {//开启
            $url_config['pass_on']='true';
            $url_config['pass']=$post_data['fw_pass'];
        }

        //到期功能-如果关闭
        if (empty($post_data['expire_time_switch'])) {
            $post_data['expire_time'] = null;
        } else {//开启
            $url_config['expire_time_on']='true';
        }

        //第三方统计功能-如果关闭
        if (empty($post_data['third_switch'])) {
            $post_data['third_count'] = null;
            $post_data['third_url'] = null;
        } else {//开启
            $url_config['third_on']='true';
        }

        //检查IP黑名单
        if (db('black')->where('ip', get_ip())->find())
            return ['code' => 500, 'msg' => '您的IP已被管理员拉黑！'];

        //URL批量操作-分割获取每行链接
        $url_list_array = explode("\n", $post_data['url']);
        $url_list = array();
        foreach ($url_list_array as $url_array) {
            if (!check_url($url_array))
                return ['code' => '500', 'msg' => '请输入正确的URL，并包含http://或https://，一行一个'];
            //检查URL黑名单
            if (db('black')->where('url', $url_array)->find())
                return ['code' => 500, 'msg' => '此长链[' . $url_array . ']已被管理员拉黑！'];
            array_push($url_list, $url_array);//获取到链接加到$url_list数组里
        }
        //生成数据
        $run_data = array(
            'type' => $post_data['type'],
            'config' => json_encode($url_config),
            'third_count' => $post_data['third_count'],
            'third_url' => $post_data['third_url'],
            'expire_time' => $post_data['expire_time'],
            'domain' => $post_data['domain']
        );
        //本网站URL
        //$http_url = 'https://' . $_SERVER['SERVER_NAME'] . '/';
        //循环批量生成
        $i = 0;
        foreach ($url_list as $url) {
            try {
                //开启异常程序
                $run = $this->url_new_arr($run_data, $url);
                if ($run['code'] == true) {
                    $code = '成功';
                    $short = $run['short'];
                    if ($post_data['domain'] == '1' || $post_data['domain'] == '2' || $post_data['domain'] == '3') {
                        $coded = $short;
                    } else {
                        $coded = $http_url . $run['coded'];
                    }
                    $msg = null;
                } else {
                    $code = '失败';
                    $short = null;
                    $coded = null;
                    $msg = $run['msg'];
                }
            } catch (Exception $e) {
                $code = '失败';
                $short = null;
                $coded = null;
                $msg = '程序异常[new003]';
            }
            //赋值到二维数组
            $re_data[] = array('coded' => $run['coded'], 'dl' => $coded, 'short' => $short, 'url' => $url, 'code' => $code, 'msg' => $msg);
            $i++;
        }
        return ['code' => 0, 'msg' => '', "count" => $i, 'data' => $re_data];
    }

    /**
     * @param $data array 生成数据
     * @param $url string URL
     * @return array 生成状态
     * @throws Exception
     */
    private function url_new_arr($data, $url)
    {
        //将URL编码
        $url_encoded = is_url_encoded($url);

        Db::startTrans();//开启事务
        try {
            //提前获取这个URL是否存在数据库，存在就进行操作
            $sql_url_check = db('user_url')->lock(true)->where('url', $url_encoded)->find();
            //存在数据库，那就直接修改数据
            if ($sql_url_check) {
                //如果不是用户的链接
                if ($sql_url_check['user'] != $this->user_data['user']) {
                    $return_data['code'] = false;
                    $return_data['msg'] = '此短链已被其他用户生成，请生成到业务短链';
                }
                /*
                //是用户链接，生成修改数组，开始修改
                $update_data=[
                    'type'=>$data['type'],
                    'short'=>$sql_url_check['short'],
                    'config'=>$data['config'],
                    'third_count'=>$data['third_count'],
                    'third_url'=>$data['third_url'],
                    'expire_time'=>$data['expire_time']
                ];
                if (Db::name('user_url')->lock(true)->where('coded', $sql_url_check['coded'])->update($update_data)){
                    //返回短链编码，和第三方短链
                    $return_data['code']=true;
                    $return_data['coded']=$sql_url_check['coded'];
                    $return_data['short']=$sql_url_check['short'];
                }else{
                    $return_data['code']=false;
                    $return_data['msg']='短链生成失败！[001]';
                }
                */
                $return_data['code'] = false;
                $return_data['msg'] = '此短链已生成，请生成到业务短链，或前往链接列表修改相关配置';
                // 提交事务
                Db::commit();
                return $return_data;
            } else {
                //不存在表，重新生成

                //获取二次跳转的域名，用于生成到第三方平台
                $domain_type = json_decode($this->config['domain_type'], true);
                $http_url = $this->config['Response_protocol'] . $domain_type[$this->config['appoint_domain_type']] . '/';
                //短链编码
                $coded = url_coded_short($url, $this->user_data['user']);
                //开始生成第三方短链
                if ($data['domain'] == '1') {//suo.im
                    //判断
                    if (empty($this->user_data['suo_key']) || empty($this->user_data['suo_time'])) {
                        $return_data['code'] = false;
                        $return_data['msg'] = '请先到[链接配置]配置suo.im生成选项';
                        return $return_data;
                    }
                    $short = suo_url($http_url . $coded, $this->user_data['suo_key'], $this->user_data['suo_time']);
                    $wx_check_url = $short;
                } elseif ($data['domain'] == '2') {
                    //判断
                    if ($this->user_data['w_url'] < 0) {
                        $return_data['code'] = false;
                        $return_data['msg'] = '您的w.url.cn生成次数已空，请充值';
                        return $return_data;
                    }
                    $short = w_url_url($http_url . $coded, $this->user_data['user']);
                    $wx_check_url = $short;
                } elseif ($data['domain'] == '3') {
                    //判断
                    if ($this->user_data['vip_type'] < 2) {
                        $return_data['code'] = false;
                        $return_data['msg'] = '非会员不可使用';
                        return $return_data;
                    }
                    $short = douyin_url($http_url . $coded);
                    $wx_check_url = $short;
                } else {
                    $short = null;
                    $wx_check_url = $http_url . $coded;
                }

                if ($this->user_data['vip_type'] > 1) {
                    $wx_check_url_sql = w_url_url($wx_check_url, $this->user_data['user'], false);
                } else
                    $wx_check_url_sql = null;

                //生成添加数组，开始添加
                $add_data = array(
                    'coded' => $coded,
                    'user' => $this->user_data['user'],
                    'ip' => Request::instance()->ip(),
                    'type' => $data['type'],
                    'short' => $short,
                    'url' => $url_encoded,
                    'check_url' => $wx_check_url_sql,
                    'config' => $data['config'],
                    'third_count' => $data['third_count'],
                    'third_url' => $data['third_url'],
                    'expire_time' => $data['expire_time']
                );

                if (Db::name('user_url')->lock(true)->insert($add_data)) {
                    //返回短链编码，和第三方短链
                    $return_data['code'] = true;
                    $return_data['coded'] = $coded;
                    $return_data['short'] = $short;
                } else {
                    $return_data['code'] = false;
                    $return_data['msg'] = '短链生成失败！[new002]';
                }
                // 提交事务
                Db::commit();
                return $return_data;
            }

        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }

    public function new_work_url()
    {
        if (!Request::instance()->isPost())
            return ['code' => 50000, 'msg' => '非法请求,no post'];
        if (!Request::instance()->isAjax())
            return ['code' => 50000, 'msg' => '非法请求,no ajax'];
        $post_data = Request::instance()->post()['data'];
        //url配置
        $url_config = array();

        if (empty($post_data['url']))
            return ['code' => 400, 'msg' => '请输入需要生成的长链接'];

        if (empty($post_data['identification'])) {
            if (empty($this->user_data['identification']))
                return ['code' => 400, 'msg' => '您还未配置业务标识'];
            $post_data['identification'] = $this->user_data['identification'];
        } else {
            if (!check_identification($post_data['identification']))
                return ['code' => 400, 'msg' => '请输入正确的业务标识'];
        }


        //开始判断第三方短链
        if (!array_key_exists($post_data['domain'], json_decode($this->config['domain_type'], true)))
            return ['code' => 400, 'msg' => '请输入选择正确的短链域名'];

        if ($post_data['domain'] == '1') {//suo.im
            if (empty($this->user_data['suo_key']) || empty($this->user_data['suo_time']))
                return ['code' => 500, 'msg' => '请先到[链接配置]配置suo.im生成选项'];
        } elseif ($post_data['domain'] == '2') {
            if ($this->user_data['w_url'] < 0)
                return ['code' => 500, 'msg' => '您的w.url.cn生成次数已空，请充值'];
        } elseif ($post_data['domain'] == '3') {
            if ($this->user_data['vip_type'] < 2)
                return ['code' => 500, 'msg' => '非会员不可使用[app.10086.cn]'];
        } else {
            //使用其他
            $domain_type = json_decode($this->config['domain_type'], true);
            $http_url = $this->config['Response_protocol'] . $domain_type[$post_data['domain']] . '/';
        }
        //判断短链属性
        if (!array_key_exists($post_data['type'], json_decode($this->config['url_type'], true)))
            return ['code' => 400, 'msg' => '请输入选择正确的短链属性'];

        //判断是否存在预览标题与小标题
        if (in_array($post_data['type'], [3, 4, 5])) {
            //不存在则使用用户配置的
            if (empty($post_data['plain_title']))
                $post_data['plain_title'] = $this->user_data['plain_title'];
            //存在就添加到URL配置
            else{
                $url_config['plain_title_on']='true';
                $url_config['plain_title']=$post_data['plain_title'];
            }

            //不存在则使用用户配置的
            if (empty($post_data['plain_text']))
                $post_data['plain_text'] = $this->user_data['plain_text'];
            //存在就添加到URL配置
            else{
                $url_config['plain_text_on']='true';
                $url_config['plain_text']=$post_data['plain_text'];
            }
        }

        //判断是否存在防红页面提示
        if ($post_data['type'] == '2') {
            //不存在则使用用户配置的
            if (empty($post_data['html_tips']))
                $post_data['html_tips'] = $this->user_data['html_tips'];
            //存在就添加到URL配置
            else{
                $url_config['html_tips_on']='true';
                $url_config['html_tips']=$post_data['html_tips'];
            }
        }

        //访问次数限制功能
        if ($post_data['type'] == '6') {
            if (!is_numeric($post_data['often']))
                return ['code' => 400, 'msg' => '请输入正确的访问数量，各位为数字'];
            $url_config['often_on']='true';
            $url_config['often']=$post_data['often'];
        }

        //密码访问功能-如果关闭
        if (empty($post_data['pass_switch'])) {
            $post_data['fw_pass'] = null;
        } else {//开启
            $url_config['pass_on']='true';
            $url_config['pass']=$post_data['fw_pass'];
        }

        //到期功能-如果关闭
        if (empty($post_data['expire_time_switch'])) {
            $post_data['expire_time'] = null;
        } else {//开启
            $url_config['expire_time_on']='true';
        }

        //第三方统计功能-如果关闭
        if (empty($post_data['third_switch'])) {
            $post_data['third_count'] = null;
            $post_data['third_url'] = null;
        } else {//开启
            $url_config['third_on']='true';
        }

        //检查IP黑名单
        if (db('black')->where('ip', get_ip())->find())
            return ['code' => 500, 'msg' => '您的IP已被管理员拉黑！'];
        //参数自定义功能-如果关闭
        if (empty($post_data['custom_switch'])) {
            $post_data['custom_id'] = null;
            $post_data['custom_url'] = null;
            //关闭即可使用批量生成-如果没关闭使用批量，会导致服务器奔溃

            //URL批量操作-分割获取每行链接
            $url_list_array = explode("\n", $post_data['url']);
            $url_list = array();
            foreach ($url_list_array as $url_array) {
                if (!check_url($url_array))
                    return ['code' => '500', 'msg' => '请输入正确的URL，并包含http://或https://，一行一个'];
                //检查URL黑名单
                if (db('black')->where('url', $url_array)->find())
                    return ['code' => 500, 'msg' => '此长链[' . $url_array . ']已被管理员拉黑！'];
                array_push($url_list, $url_array);//获取到链接加到$url_list数组里
            }

        } else {//开启echo $url_config[0]['custom_data']['2'];
            //先判断主链接是否规范
            if (!check_url($post_data['url']))
                return ['code' => '500', 'msg' => '请输入正确的URL，并包含http://或https://'];
            //先判断标识是否符合规范
            foreach ($post_data['custom_id'] as $custom_id) {
                if (!check_identification($custom_id))
                    return ['code' => 400, 'msg' => '请输入正确的标识'];
            }
            //先判断标识对应链接是否符合规范
            foreach ($post_data['custom_url'] as $custom_url) {
                if (!check_url($custom_url))
                    return ['code' => 400, 'msg' => '请输入正确的标识对应URL，并包含http://或https://'];
            }
            //合并
            $custom_data = array_combine($post_data['custom_id'], $post_data['custom_url']);
            $url_config['custom_on']='true';
            $url_config['custom_data']=$custom_data;
            //array_push($url_config, ['custom_on' => true, 'custom_data' => $custom_data]);
        }
        //生成数据
        $run_data = array(
            'type' => $post_data['type'],
            'config' => json_encode($url_config),
            'third_count' => $post_data['third_count'],
            'third_url' => $post_data['third_url'],
            'expire_time' => $post_data['expire_time'],
            'domain' => $post_data['domain']
        );

        //循环批量生成
        $i = 0;
        if (empty($post_data['custom_switch'])) {
            foreach ($url_list as $url) {
                try {
                    //开启异常程序
                    $run = $this->work_new_arr($run_data, $url, $post_data['identification']);
                    if ($run['code'] == true) {
                        $code = '成功';
                        $short = $run['short'];
                        if ($post_data['domain'] == '1' || $post_data['domain'] == '2') {
                            $coded = $short;
                        } else {
                            $coded = $http_url . $run['coded'];
                        }
                        $msg = null;
                    } else {
                        $code = '失败';
                        $short = null;
                        $coded = null;
                        $msg = $run['msg'];
                    }
                } catch (Exception $e) {
                    $code = '失败';
                    $short = null;
                    $coded = null;
                    $msg = '程序异常[new003]';
                }
                //赋值到二维数组
                $re_data[] = array('dl' => $coded, 'short' => $short, 'url' => $url, 'code' => $code, 'msg' => $msg);
                $i++;
            }
        } else {
            $url = explode("\n", $post_data['url'])[0];
            try {
                //开启异常程序
                $run = $this->work_new_arr($run_data, $url, $post_data['identification']);
                if ($run['code'] == true) {
                    $code = '成功';
                    $short = $run['short'];
                    $coded = $http_url . $run['coded'];
                    $msg = null;
                } else {
                    $code = '失败';
                    $short = null;
                    $coded = null;
                    $msg = $run['msg'];
                }
            } catch (Exception $e) {
                $code = '失败';
                $short = null;
                $coded = null;
                $msg = '程序异常[new003]';
            }
            //赋值到二维数组
            $re_data[] = array('dl' => $coded, 'short' => $short, 'url' => $url, 'code' => $code, 'msg' => $msg);
            $i++;
        }


        return ['code' => 0, 'msg' => '', "count" => $i, 'data' => $re_data];
    }

    private function work_new_arr($data, $url, $identification)
    {
        //将URL编码
        $url_encoded = is_url_encoded($url);

        Db::startTrans();//开启事务
        try {
            //获取二次跳转的域名，用于生成到第三方平台
            $domain_type = json_decode($this->config['domain_type'], true);
            $http_url = $this->config['Response_protocol'] . $domain_type[$this->config['appoint_domain_type']] . '/';
            //短链编码-增加随机KEY，防止重复
            $coded = url_coded_short($url, $this->user_data['user'] . rand(9999, 99999));
            //开始生成第三方短链
            if ($data['domain'] == '1') {//suo.im
                //判断
                if (empty($this->user_data['suo_key']) || empty($this->user_data['suo_time'])) {
                    $return_data['code'] = false;
                    $return_data['msg'] = '请先到[链接配置]配置suo.im生成选项';
                    return $return_data;
                }
                $short = suo_url($http_url . $coded, $this->user_data['suo_key'], $this->user_data['suo_time']);
                $wx_check_url = $short;
            } elseif ($data['domain'] == '2') {
                //判断
                if ($this->user_data['w_url'] < 0) {
                    $return_data['code'] = false;
                    $return_data['msg'] = '您的w.url.cn生成次数已空，请充值';
                    return $return_data;
                }
                $short = w_url_url($http_url . $coded, $this->user_data['user']);
                $wx_check_url = $short;
            } elseif ($data['domain'] == '3') {
                //判断
                if ($this->user_data['vip_type'] < 2) {
                    $return_data['code'] = false;
                    $return_data['msg'] = '非会员不可使用';
                    return $return_data;
                }
                $short = douyin_url($http_url . $coded);
                $wx_check_url = $short;
            } else {
                $short = null;
                $wx_check_url = $http_url . $coded;
            }

            if ($this->user_data['vip_type'] > 1) {
                $wx_check_url_sql = w_url_url($wx_check_url, $this->user_data['user'], false);
            } else
                $wx_check_url_sql = null;

            //生成添加数组，开始添加
            $add_data = array(
                'coded' => $identification . $coded,//加入业务标识
                'user' => $this->user_data['user'],
                'ip' => Request::instance()->ip(),
                'type' => $data['type'],
                'short' => $short,
                'url' => $url_encoded,
                'check_url' => $wx_check_url_sql,
                'config' => $data['config'],
                'third_count' => $data['third_count'],
                'third_url' => $data['third_url'],
                'expire_time' => $data['expire_time']
            );

            if (Db::name('user_work_url')->lock(true)->insert($add_data)) {
                //返回短链编码，和第三方短链
                $return_data['code'] = true;
                $return_data['coded'] = $coded;
                $return_data['short'] = $short;
            } else {
                $return_data['code'] = false;
                $return_data['msg'] = '短链生成失败！[new002]';
            }
            // 提交事务
            Db::commit();
            return $return_data;

        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }

    /**
     * @example 批量检测短链拦截情况
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function check_intercept_url()
    {
        if (!Request::instance()->isPost())
            return ['code' => 50000, 'msg' => '非法请求,no post'];
        if (!Request::instance()->isAjax())
            return ['code' => 50000, 'msg' => '非法请求,no ajax'];
        if ($this->user_data['vip_type'] < 2)
            return ['code' => 60000, 'msg' => '非会员不可操作'];
        $post_data = Request::instance()->post();
        if ($post_data['table'] == 'user')
            $db_table='user_url';
        elseif ($post_data['table']=='work')
            $db_table='user_work_url';
        else
            $db_table='user_url';
        $http_url=$this->config['Response_protocol'].json_decode($this->config['domain_type'],true)[$this->config['appoint_domain_type']].'/';
        //单检测
        $msg = null;
        if ($post_data['type']=='1'){
            $url_data = db($db_table)->where('coded',$post_data['data'])->find();
            if (empty($url_data))
                return ['code' => 400, 'msg' => '数据信息不存在！'];
            //没有检测链接
            if (empty($url_data['check_url'])) {
                $check_url = w_url_url($http_url . $url_data['coded'], $this->user_data['user'], false);
                db($db_table)->where('coded', $url_data['coded'])->update(['check_url' => $check_url]);
            } else//有检测链接
                $check_url = $url_data['check_url'];
            //没有第三方短链
            if (empty($url_data['short']))
                $ber_short_url = $http_url . $url_data['coded'];
            else//有
                $ber_short_url=$url_data['short'];
            //检测QQ
            if (!check_intercept_qq($ber_short_url)) {
                $msg .= '已被QQ拦截、';
            } else {
                $msg .= 'QQ正常、';
            }
            //检测微信
            if (check_intercept_wx($check_url)) {
                $msg .= '已被微信拦截';
            } else {
                $msg .= '微信正常';
            }
            return ['code' => 200, 'msg' => $msg];
        }elseif ($post_data['type']=='2'){
            $data_json = json_decode($post_data['data'], true);
            if (empty($data_json))
                return ['code' => 400, 'msg' => '请至少选择一条数据'];
            $i = 0;
            foreach ($data_json as $data) {
                //获取URL信息
                $url_data = db($db_table)->where('coded', $data['coded'])->find();
                if (empty($url_data)){
                    $code = 0;
                    $msg .= '数据不存在';
                }
                //没有第三方短链
                if (empty($url_data['short']))
                    $ber_short_url = $http_url . $url_data['coded'];
                else//有
                    $ber_short_url=$url_data['short'];
                //检测QQ
                if (!check_intercept_qq($ber_short_url)) {
                    $code = 1;
                    $msg .= '已被QQ拦截、';
                } else {
                    $code = 0;
                    $msg .= 'QQ正常、';
                }
                //没有检测链接
                if (empty($url_data['check_url'])) {
                    $check_url = w_url_url($http_url . $url_data['coded'], $this->user_data['user'], false);
                    db($db_table)->where('coded', $url_data['coded'])->update(['check_url' => $check_url]);
                } else//有检测链接
                    $check_url = $url_data['check_url'];
                //检测微信
                if (check_intercept_wx($check_url)) {
                    $code = 1;
                    $msg .= '已被微信拦截';
                } else {
                    $code = 0;
                    $msg .= '微信正常';
                }

                /*$code = 0;
                $msg .= 'QQ正常、';
                $msg .= '微信正常1';*/
                $i++;
                $re_data[] = array('dl' => $data['coded'], 'short' => $data['short'], 'url' => $data['url'], 'code' => $code, 'msg' => $msg,'state'=>$url_data['state']);
            }
            return ['code' => 200, 'msg' => '', "count" => $i, 'data' => $re_data];
        }
    }

    /**
     * @example 暂停单个或多个短链
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function suspend_url(){
        if (!Request::instance()->isPost())
            return ['code' => 50000, 'msg' => '非法请求,no post'];
        if (!Request::instance()->isAjax())
            return ['code' => 50000, 'msg' => '非法请求,no ajax'];
        $post_data = Request::instance()->post();
        if ($post_data['table'] == 'user')
            $db_table='user_url';
        elseif ($post_data['table']=='work')
            $db_table='user_work_url';
        else
            $db_table='user_url';
        //单暂定
        if ($post_data['type']=='1'){
            $url_data = db($db_table)->where('coded',$post_data['data'])->value('state');
            if (in_array($url_data, [2,3]))
                return ['code'=>400,'msg'=>'当前链接状态不支持暂停访问'];
            db($db_table)->where('coded', $post_data['data'])->update(['state' => 1]);
            return ['code' => 200, 'msg' => ''];
        }
        //多暂停
        elseif ($post_data['type']=='2'){
            $data_json = json_decode($post_data['data'], true);
            foreach ($data_json as $data){
                $url_data = db($db_table)->where('coded',$data['coded'])->find();
                if ($url_data==null)
                    return ['code'=>400,'msg'=>'数据消息不存在'];
                if (in_array($url_data, [2,3]))
                    return ['code'=>400,'msg'=>'当前链接状态不支持暂停访问'];
                db($db_table)->where('coded', $data['coded'])->update(['state' => 1]);
            }
            return ['code' => 200, 'msg' => ''];
        }
    }

    /**
     * @example 恢复单个或多个短链
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function recovery_url(){
        if (!Request::instance()->isPost())
            return ['code' => 50000, 'msg' => '非法请求,no post'];
        if (!Request::instance()->isAjax())
            return ['code' => 50000, 'msg' => '非法请求,no ajax'];
        $post_data = Request::instance()->post();
        if ($post_data['table'] == 'user')
            $db_table='user_url';
        elseif ($post_data['table']=='work')
            $db_table='user_work_url';
        else
            $db_table='user_url';
        //单暂定
        if ($post_data['type']=='1'){
            $url_data = db($db_table)->where('coded',$post_data['data'])->value('state');
            if ($url_data==null)
                return ['code'=>400,'msg'=>'数据消息不存在'];
            if (in_array($url_data, [2,3]))
                return ['code'=>400,'msg'=>'当前链接状态不支持恢复访问'];
            db($db_table)->where('coded', $post_data['data'])->update(['state' => 0]);
            return ['code' => 200, 'msg' => ''];
        }
        //多暂停
        elseif ($post_data['type']=='2'){
            $data_json = json_decode($post_data['data'], true);
            foreach ($data_json as $data){
                $url_data = db($db_table)->where('coded',$data['coded'])->value('state');
                if ($url_data==null)
                    return ['code'=>400,'msg'=>'数据消息不存在'];
                if (in_array($url_data, [2,3]))
                    return ['code'=>400,'msg'=>'当前链接状态不支持恢复访问'];
                db($db_table)->where('coded', $data['coded'])->update(['state' => 0]);
            }
            return ['code' => 200, 'msg' => ''];
        }
    }

    /**
     * @example 删除单个或多个短链
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del_url(){
        if (!Request::instance()->isPost())
            return ['code' => 50000, 'msg' => '非法请求,no post'];
        if (!Request::instance()->isAjax())
            return ['code' => 50000, 'msg' => '非法请求,no ajax'];
        $post_data = Request::instance()->post();
        if ($post_data['table'] == 'user')
            $db_table='user_url';
        elseif ($post_data['table']=='work')
            $db_table='user_work_url';
        else
            $db_table='user_url';
        //单删
        if ($post_data['type']=='1'){
            $url_data = db($db_table)->where('coded',$post_data['data'])->find();
            if ($url_data==null)
                return ['code'=>400,'msg'=>'数据消息不存在'];
            if (in_array($url_data, [2,3]))
                return ['code'=>400,'msg'=>'当前链接状态不支持删除'];
            db($db_table)->where('coded', $post_data['data'])->delete();
            return ['code' => 200, 'msg' => ''];
        }
        //多删除
        elseif ($post_data['type']=='2'){
            $data_json = json_decode($post_data['data'], true);
            foreach ($data_json as $data){
                $url_data = db($db_table)->where('coded',$data['coded'])->value('state');
                if ($url_data==null)
                    return ['code'=>400,'msg'=>'数据消息不存在'];
                if (in_array($url_data, [2,3]))
                    return ['code'=>400,'msg'=>'当前链接状态不支持删除'];
                db($db_table)->where('coded', $data['coded'])->delete();
            }
            return ['code' => 200, 'msg' => ''];
        }
    }

    public function update_url(){
        if (!Request::instance()->isPost())
            return ['code' => 50000, 'msg' => '非法请求,no post'];
        if (!Request::instance()->isAjax())
            return ['code' => 50000, 'msg' => '非法请求,no ajax'];
        $post_data = Request::instance()->post();
        $url_config=array();

        if ($post_data['table']=='work'){
            return ['code'=>200,'msg'=>'这他妈是业务表，你他妈参数写错了傻逼'];
        }

        if (!array_key_exists($post_data['type'], json_decode($this->config['url_type'], true)))
            return ['code' => 400, 'msg' => '请输入选择正确的短链属性'];

        //判断是否存在预览标题与小标题
        if (in_array($post_data['type'], [3, 4, 5])) {
            //不存在则使用用户配置的
            if (empty($post_data['plain_title']))
                $post_data['plain_title'] = $this->user_data['plain_title'];
            else{//存在就添加到URL配置
                $url_config['plain_title_on']='true';
                $url_config['plain_title']=$post_data['plain_title'];
            }

            //不存在则使用用户配置的
            if (empty($post_data['plain_text']))
                $post_data['plain_text'] = $this->user_data['plain_text'];
            else{
                //存在就添加到URL配置
                $url_config['plain_text_on']='true';
                $url_config['plain_text']=$post_data['plain_text'];
            }
        }

        //判断是否存在防红页面提示
        if ($post_data['type'] == '2') {
            //不存在则使用用户配置的
            if (empty($post_data['html_tips']))
                $post_data['html_tips'] = $this->user_data['html_tips'];
            else{//存在就添加到URL配置
                $url_config['html_tips_on']='true';
                $url_config['html_tips']=$post_data['html_tips'];
            }
        }

        //密码访问功能-如果关闭
        if (empty($post_data['pass_switch'])) {
            $post_data['fw_pass'] = null;
        } else {//开启
            $url_config['pass_on']='true';
            $url_config['pass']=$post_data['fw_pass'];
        }

        //到期功能-如果关闭
        if (empty($post_data['expire_time_switch'])) {
            $post_data['expire_time'] = null;
        } else {//开启
            $url_config['expire_time_on']='true';
        }

        //第三方统计功能-如果关闭
        if (empty($post_data['third_switch'])) {
            $post_data['third_count'] = null;
            $post_data['third_url'] = null;
        } else {//开启
            $url_config['third_on']='true';
        }
        $User_url=new Userurl();
        $update_array=array(
            'url'=>$post_data['url'],
            'type'=>$post_data['type'],
            'third_count'=>$post_data['third_count'],
            'third_url'=>$post_data['third_url'],
            'expire_time'=>$post_data['expire_time'],
            'config'=>json_encode($url_config)
        );
        if ($User_url->update_user_url($update_array,$post_data['coded']))
            return ['code'=>200];
        return ['code'=>400,'msg'=>'保存修改失败'];
    }
}