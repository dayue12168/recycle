<?php
/**
 * Created by PhpStorm.
 * User: dayue
 * Date: 2019/3/25
 * Time: 17:27
 */

namespace app\v1\controller;

use OAuth2\Autoloader;
use think\Controller;
use think\Db;

class OAuth extends Controller
{

    public function _initialize()
    {
        Autoloader::register();
    }

    private function server()
    {
        $pdo = new \PDO('mysql:host=rm-uf6qd1p0j4dv12t59vo.mysql.rds.aliyuncs.com;dbname=recycle_aliyun', "recycle_admin", "QWER!@#$%4321");

        //创建存储的方式
        $storage = new \OAuth2\Storage\Pdo($pdo);

        //创建server
        $server = new \OAuth2\Server($storage);

        // 添加 Authorization Code 授予类型
        $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));

        return $server;
    }

    public function authorize()
    {
        // global $server;
        // $dsn = 'mysql:dbname=recycle;host=rm-uf6qd1p0j4dv12t59vo.mysql.rds.aliyuncs.com';
        // $username = 'recycle_admin';
        // $password = 'QWER!@#$%4321';
        // Autoloader::register();
        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $server = $this->server();
        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();
        #echo '<pre/>';
        #var_dump($response);die('aaa');
        // validate the authorize request
        if (!$server->validateAuthorizeRequest($request, $response)) {
            $response->send();
            die;
        }
        // display an authorization form
        #if (empty($_POST)) {
        #    exit('
        #<form method="post">
        #  <label>Do You Authorize Client?</label><br />
        #  <input type="submit" name="authorized" value="yes">
        #  <input type="submit" name="authorized" value="no">
        #</form>');
        #}

        // print the authorization code if the user has authorized your client
	#$is_authorized = ($_POST['authorized'] === 'yes');
	$is_authorized = true;
        // echo "<pre/>";
        // print_r($_GET);
        // die();
        $server->handleAuthorizeRequest($request, $response, $is_authorized);
        if ($is_authorized) {
            // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
            $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);
            // exit("SUCCESS! Authorization Code: $code");
            echo $code;
        }
        $response->send();
    }


    // 生成并获取token
    public function token()
    {
        $server = $this->server();
        $server->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
        exit();
    }

    // 客户端回调，来自server端的Location跳转到此
// 此处会携带上code和你自定义的state
    public function cb()
    {
        $request = \OAuth2\Request::createFromGlobals();
        $url = "https://lg.nineseatech.com/token";
        $data = [
            'grant_type' => 'authorization_code',
            'code' => $request->query('code'),
            'client_id' => 'jiuhai',
            'client_secret' => 'Wkcxa2NsSlhWakphV0VKM1VXNWtSR1ZzUWtsalp6MDk',
            'redirect_uri' => 'https://lg.nineseatech.com/cb/'
        ];

        //todo 自定义的处理判断
        $state = $request->query('state');
        # echo '<pre/>';
        # var_dump($data);die('---');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        //执行请求
        $output = curl_exec($ch);
        //打印获得的数据
        $output = json_decode($output,true);
        // print_r($output);die();

        // 获取user_id,
        if(is_array($output)){
            $access_token = $output['access_token'];
        }else{
            echo "FAILED! Authorization Code";exit;
        }
        $db_access_token = Db::table('oauth_access_tokens')->where('client_id',$data['client_id'])->column('access_token');
        // print_r($db_access_token);die();
        if(in_array($access_token, $db_access_token)){
            // 免密登录
            $user_id = Db::table('oauth_clients')->where('client_id',$data['client_id'])->value('user_id');
            $data = Db::table('jh_user')->field('tel,psw,user_name')->where('user_id',$user_id)->find();
            session('adminUser',$data['tel']);
            // print_r(session('adminUser'));die();
            return redirect('https://lg.nineseatech.com/admin/Index/index');
        }else{
        echo "FAILED! Authorization Code";exit;
        }

        // $response = Curl::ihttp_post($url, $data);
        // if (is_error($response)) {
        //     var_dump($response);
        // }

        // var_dump($response['content']);
    }



    // client
    public function getAuthorize(){
        return redirect('/authorize?response_type=code&client_id=jiuhai&redirect_uri=https://lg.nineseatech.com/cb/&state=123456&response_type=code');
    }


    // 测试资源
    public function res1()
    {
        $server = $this->server();
        // Handle a request to a resource and authenticate the access token
        if (!$server->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
            $server->getResponse()->send();
            die;
        }

        $token = $server->getAccessTokenData(\OAuth2\Request::createFromGlobals());
print_r($token);
        $scopes = explode(" ", $token['scope']);

        // todo 这里你可以写成自己规则的scope验证
        if (!$this->checkScope('basic', $scopes)) {
            return json_encode(['success' => false, 'message' => '你没有获取该接口的scope']);
        }

        return json_encode(['success' => true, 'message' => '你成功获取该接口信息', 'token'=>$token]);
    }

    // 用于演示检测scope的方法
    private function checkScope($myScope, $scopes)
    {
        return in_array($myScope, $scopes);
    }
}
