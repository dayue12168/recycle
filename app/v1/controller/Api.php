<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2019/2/28
 * Time: 16:57
 接口示例{"success":"0","message":"失败原因","sign":"jiuhai", "result":{"dustbintotal":"100",
 "binlist":[{"longitude":"123.12","latitude":"456.45"},{"longitude":"321.32","latitude":"654.65"}],
 "captotal":"50","caponline":"48","capoffline":"2",
 "offlinelist":[{"longitude":"123.12","latitude":"456.45"},{"longitude":"321.32","latitude":"654.65"}],
 "dust1":"10000","dust7":"70000","overflownum":"2",
 "overflowlist":[{"longitude":"123.12","latitude":"456.45"},{"longitude":"321.32","latitude":"654.65"}]}}
 success:1表示成功
sign:签名，固定为 jiuhai
longitude:经度
latitude:纬度
dustbintotal:垃圾桶总数
binlist:垃圾桶位置清单
captotal:设备总数
caponline:在线设备总数
capoffline:离线设备总数
offlinelist:离线设备位置清单
dust1:最近1天垃圾总
dust7:最近7天垃圾总
overflownum:满溢垃圾桶总数
overflowlist:满溢垃圾桶位置清单
 */

namespace app\v1\controller;

use lib\aliyun\Demo;
use think\Db;
use think\Request;

define("OVERRATE",0.9);	//溢出的鉴定比率，超过该比率认为溢出
define("OVERRATETORECYCLE",0.5);	//从溢出到回收的鉴定比率，在溢出状态高度低于这个认为是进行了回收，大于等于则保持溢出
define("RECYCLEHEIGHT",0.1);	//高度小于该比率，认为是回收的条件1
define("RECYCLEDIFF",0.1);	//2次采集相差高度大于该比率，认为是回收的条件2

class Api
{
    private $appKey = "25264176";
    private $appSecret = "c4204a1608924786b6e1ce58ec6d813f";
    //协议(http或https)://域名:端口，注意必须有http://或https://
    // private static $host = "http://api.st-saas.com/api/api.ashx";
    // private $host = "https://api.st-saas.com/API/api.ashx";
    private $host = "https://api.st-saas.com";
    
    //设备调用的信息
    private $capappKey = '25934813';		//appkey
    private $capappSecret = 'baf0238c94ed4111c1f2b9f102ed75ca';		//appsecret
    private $capproductKey = 'a1FMKlSx1Zj';		//产品号
    
    /*
    public function PostInfo($postdata = '')
  {
    $url = $postdata['url'];
    $header = array(
      "Content-Type: application/json",
      "Accept: application/json"
    );
    $curl = curl_init();                                             // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);                            // 要访问的地址
    curl_setopt($curl, CURLOPT_POST, 1);                              // 定义请求类型
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);                          // 设置一个长整形数，作为最大延续多少秒，设置超时限制防止死循环
    ////curl_setopt($curl, CURLOPT_HEADER, 1);                           // 0不显示返回的Header区域内容，非0显示，默认为0
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);                  // 
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postdata));   // POST提交的数据包
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);                 // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl);                                      // 执行操作
    if (curl_errno($curl)) {
      echo '操作错误：' . curl_error($curl);                          //捕抓异常
    }
      curl_close($curl);                                              // 关闭CURL会话
      echo $tmpInfo;
      die("==");
      return $postdata['imei'] . ":" . $tmpInfo;                      // 返回数据，json格式
  }
  
    public function test()
    {
    	//http://101.132.132.197/testabc
      $postdata = array();
      $postdata['update_time']  = "utime";
      $postdata['imei']         = "imei";
      $postdata['hexdata']      = "hexdata";
      $postdata['url']          = "http://101.132.132.197/auto_get_infoself";
      //$postdata['url']          = "http://www.nineseatech.com/public/api/Test";
    	$this->PostInfo($postdata);
    	
    }
    */
    
    public function bigScreen()
    {
    	//用于临港项目大屏显示
        $result = json_encode($this->bigScreenTemp());
        echo $result;
        die('');
        $res['success'] = 1;
        $res['message'] = '成功';
        $res['sign'] = 'jiuhai';
        $res['result'] = $result;
        // $res='{"success":"1","message":"接口调用成功","sign":"jiuhai","result":{"dustbintotal":"100","binlist":[{"longitude":"123.12","latitude":"456.45"},{"longitude":"321.32","latitude":"654.65"}],"captotal":"50","caponline":"48","capoffline":"2","offlinelist":[{"longitude":"123.12","latitude":"456.45"},{"longitude":"321.32","latitude":"654.65"}],"dust1":"10000","dust7":"70000","overflownum":"2","overflowlist":[{"longitude":"123.12","latitude":"456.45"},{"longitude":"321.32","latitude":"654.65"}]}}';
        echo json_encode($res);
    }

		public function dogetGarbageAlert($imei,$message,$source)
		{
			//定时溢出报警
        $path = "/API/api.ashx";
        $params = '{
                        "id": "bded4128dc454a03b3d10c45de17b863",
                        "version": "1.0",
                        "tenantId": 2,
                        "apiName": "setGarbageAlert",
                        "request": {
                        "apiVer": "1.0.0"
                        },
                        "params": {
                        "message": "'.$message.'",
                        "source": "'.$source.'"
                        }
                    }';
        print_r($params);
        $demo = new Demo($this->appKey, $this->appSecret, $this->host);
        $res = $demo->doPostString($path, $params);
				$this->writeoverlog($imei,"垃圾溢出报警结果：".$res);
        return true;			
		}

    public function getGarbageAlert()
    {
    	//定时溢出报警
    	//http://101.132.132.197/get_garbage_alert
    	//读取所有有溢出的垃圾桶
			$sql="select jc.cap_imei,jdi.gps_gd,jdi.dust_address from jh_dustbin_info jdi join jh_cap jc on jdi.cap_id=jc.cap_id where dustbin_state=0 and cap_status=0 and dustbin_overflow=1";
			$result=Db::query($sql);
			if(count($result)==0)
			{
				$this->writeoverlog("当前无溢出垃圾桶","垃圾溢出");
				//$this->dogetGarbageAlert("当前无溢出垃圾桶","当前无溢出垃圾桶","当前无溢出垃圾桶");
			}
			//循环所有
			for($i=0;$i<count($result);$i++)
			{
				$this->writeoverlog($result[$i]["cap_imei"],"垃圾溢出");
				$this->dogetGarbageAlert($result[$i]["cap_imei"],$result[$i]["dust_address"]."垃圾桶溢出","垃圾桶".$result[$i]["cap_imei"]."溢出报警");
			}
  
    }

// 调用该接口获取物（设备）的基本信息。
    public function getAppThing($capdeviceName)
    {
        $host = 'https://api.link.aliyun.com';
        $path = "/app/thing/info/get";
        $params = '{
                        "id": "bded4128dc454a03b3d10c45de17b868",
                        "version": "1.0",
                        "request": {
                            "apiVer": "1.0.0"
                        },
                        "params": {
                            "productKey": "'.$this->capproductKey.'",
                            "deviceName": "'.$capdeviceName.'"
                        }
                    }';
        $demo = new Demo($this->capappKey, $this->capappSecret, $host);
        $res = $demo->doPostString($path, $params);
        return $res;
    }

//调用该接口获取物（设备）的连接状态。
    public function getAppThingStatus()
    {
        $appKey = '25934813';
        $appSecret = 'baf0238c94ed4111c1f2b9f102ed75ca';
        $productKey = 'a1FMKlSx1Zj';
        $deviceName = '0A17100617109259';

        $host = 'https://api.link.aliyun.com';
        $path = "/app/thing/status/get";
        $params = '{
                        "id": "bded4128dc454a03b3d10c45de17b863",
                        "version": "1.0",
                        "request": {
                            "apiVer": "1.0.0"
                        },
                        "params": {
                            "productKey": "'.$productKey.'",
                            "deviceName": "'.$deviceName.'"
                        }
                    }';
        $demo = new Demo($appKey, $appSecret, $host);
        $res = $demo->doPostString($path, $params);

        return $res;
    }

    public function bigScreenTemp()
    {
    	  //垃圾桶位置清单
        $sql='select max(cap_id) as cap_id,max(longitude) as longitude,max(latitude) as latitude from jh_dustbin_info ';
        $sql.='where dustbin_state=0 group by dust_serial';

        $res=Db::query($sql);
        $result['dustbintotal']=count($res);
        $result['binlist']=$res;

        //设备总数，在线设备数，离线设备数
        $sql='select count(*) totalcap,sum(case when cap_isonline=0 then 1 else 0 end) as online,';
        $sql.='sum(case when cap_isonline=1 then 1 else 0 end) as offline from jh_cap where cap_status=0';
				$res=Db::query($sql);
				$result['captotal']=$res[0]['totalcap'];
				$result['caponline']=$res[0]['online'];
				$result['capoffline']=$res[0]['offline'];

				//离线设备清单
        $sql='select distinct jdi.cap_id,jdi.longitude,jdi.latitude from jh_dustbin_info jdi join jh_cap jc on jc.cap_id=jdi.cap_id';
        $sql.=' where jdi.dustbin_state=0 and jc.cap_status=0 and jc.cap_isonline=1';
        $res=Db::query($sql);
        $result['offlinelist']=$res;

        //最近1天，7天垃圾数量
        $enddate=date("Y-m-d",strtotime("now"));
        $startdate1=date("Y-m-d",strtotime("-1 day",strtotime("now")));
        $startdate7=date("Y-m-d",strtotime("-7 day",strtotime("now")));
        $sql="select ifnull(sum(dust_num),0) as dustnum1 from jh_rubbish_record where dust_date>='".$startdate1."' and dust_date<'".$enddate."'";
        $res=Db::query($sql);
        $result['dust1']=$res[0]['dustnum1'];
        $sql="select ifnull(sum(dust_num),0) as dustnum7 from jh_rubbish_record where dust_date>='".$startdate7."' and dust_date<'".$enddate."'";
        $res=Db::query($sql);
        $result['dust7']=$res[0]['dustnum7'];

        //满溢垃圾桶位置清单
        $sql='select max(cap_id) as cap_id,max(longitude) as longitude,max(latitude) as latitude from jh_dustbin_info ';
        $sql.='where dustbin_state=0 and dustbin_overflow=1 group by dust_serial';
        $res=Db::query($sql);
        $result['overflownum']=count($res);
        $result['overflowlist']=$res;

        return $result;
    }

    public function getApiCron(){
        $result = '['.json_encode($this->bigScreenTemp()).']';
        //echo $result;
        //$result='[{"dustbintotal":6,"binlist":[],"captotal":"18","caponline":"18","capoffline":"0","dust1":"0.00","dust7":"0.00","overflownum":0}]';
				//$result='[{"dustbintotal":6,"binlist":[{"longitude":"1234567","latitude":"12345"},{"longitude":"1234567","latitude":"12345123"},{"longitude":"1112","latitude":"1112"},{"longitude":"111","latitude":"1112"},{"longitude":"1112","latitude":"1112"},{"longitude":"1112","latitude":"1112"}],"captotal":"18","caponline":"18","capoffline":"0","offlinelist":[],"dust1":"0.00","dust7":"0.00","overflownum":0,"overflowlist":[]}]';
        //初始化
        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, 'http://106.14.198.243:8060/karteMarkieren-api-1.0-SNAPSHOT/data/upload');
        //设置头文件的信息作为数据流输出
        //curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        //$post_data = array(
        //    "appkey" => 3,
        //    "detail" => urlencode($result)
        //    );
        //$post_data['appkey']=3;
        //$post_data['detail']=urlencode($result);
        $post_data="appkey=3&detail=".urlencode($result);
         //echo $post_data;
         //die();

        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        //执行命令
        $data = curl_exec($curl);

        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        print_r($data);
    }

    // 生产租户URI
    public function create(Request $request){
        // $signHeaders = Request::instance()->header('X-Ca-Signature-Headers');
        // var_dump($signHeaders);die('---');
        $tenantId = $request->param('tenantId');
        if(!isset($tenantId)){
            $result =  array(
                'code' => 203,
                'message' => '传入参数有误！'
            );
            return json_encode($result);
        }
        $data = $request->param();
        $fileName = 'createLog.txt';
        $this->logWrite($fileName, $data);

        // $tenantId = 'a1f36ae1a35f4359a12b474b96fb838d';
        $userId = random(29,'string',1);
        $tel = (string)(time().mt_rand(0,9));
        $psw = md5(123456);
        $user_name = '租户'.mt_rand(0,999999999);
        $data = [
            'tel' => $tel,
            'psw' => $psw,
            'user_name' => $user_name,
            'role_id' => 2,
            'tenantId' => $tenantId,
            'userId' => $userId
        ];
        $res = Db::table('jh_user')->insert($data);

        if($res){
            $result =  array(
                'code' => 200,
                'message' => 'success',
                'userId' => $userId
            );
        }else{
            $result =  array(
                'code' => 203,
                'message' => '传入参数有误！'
            );
        }

        return json_encode($result);
    }

    //注销租户URI
    public function delete(Request $request){
        $data = $request->param();
        $fileName = 'deleteLog.txt';
        $this->logWrite($fileName, $data);
        $tenantId = $request->param('tenantId');
        $userId = $request->param('userId');
        if(!isset($tenantId) || !isset($userId)){
            $result =  array(
                'code' => 203,
                'message' => '传入参数有误！'
            );
            return json_encode($result);
        }
        // $tenantId = 'a1f36ae1a35f4359a12b474b96fb838d';
        // $userId = 'EAT63ZPFTC8CZ8MVA6SNS2CAQFFNF';

        $res = Db::table('jh_user')
            ->where('tenantId',$tenantId)
            ->where('userId',$userId)
            ->delete();

        if ($res) {
            $result =  array(
                'code' => 200,
                'message' => 'success'
            );
        }else{
            $result =  array(
                'code' => 203,
                'message' => '传入参数有误！'
            );
        }

        return json_encode($result);
    }

    // 免密登录URI
    public function getSSOUrl(Request $request){
        // 接收参数

        $data = $request->param();
        if(empty($data)){
            $result =  array(
                'code' => 203,
                'message' => '传入参数有误！'
            );
            return json_encode($result);
        }
        $fileName = 'getSSOUrLog.txt';
        $this->logWrite($fileName, $data);
        //为用户生成临时token
        $token = genToken();
        #var_dump($token);
        // 验证参数
        $tenantId = Db::table('jh_user')->where('tenantId',$data['tenantId'])->value('tenantId');

        $userId = Db::table('jh_user')->where('userId',$data['userId'])->value('userId');
         if(empty($tenantId) || empty($userId)){
             $result =  array(
                 'code' => 203,
                 'message' => 'tenantId或者userId传入有误！'
             );
         }else{

            // token更新进jh_user表中
            $token_update = Db::table('jh_user');
	    Db::table('jh_user')->where('userId', $data['userId'])->update(['token' => $token]);
            $result = array(
                'code' => 200,
                'message' => 'success',
                'ssoUrl' => "https://lg.nineseatech.com/sso?userId=".$userId."&ssoToken=".$token.""
            );
        }

        return json_encode($result);
    }

    public function sso(Request $request){

        $userId = $request->param('userId');
        $token = $request->param('ssoToken');
        $db_token = Db::table('jh_user')->where('userId', $userId)->value('token');
        if($token === $db_token){
            $data = Db::table('jh_user')->field('tel')->where('userId', $userId)->find();
            session('adminUser',$data['tel']);
            // print_r(session('adminUser'));die();
            return redirect('https://lg.nineseatech.com/admin/Index/index');
        }else{
            return 'token验证失败！';
        }

    }


    public function getAppThingProperties($capdeviceName)
    {

        //$appKey = '25934813';
        //$appSecret = 'baf0238c94ed4111c1f2b9f102ed75ca';

        $host = 'https://api.link.aliyun.com';
        $path = "/app/thing/properties/get";
        $params = '{
                        "id": "bded4128dc454a03b3d10c45de17b863",
                        "version": "1.0",
                        "request": {
                            "apiVer": "1.0.0"
                        },
                        "params": {
                            "productKey": "'.$this->capproductKey.'",
                            "deviceName": "'.$capdeviceName.'"
                        }
                    }';
        $demo = new Demo($this->capappKey, $this->capappSecret, $host);
//        return $params;
        $res = $demo->doPostString($path, $params);

        return $res;
    }

    public function getAppThingEventTimeline()
    {
        $appKey = '25934813';
        $appSecret = 'baf0238c94ed4111c1f2b9f102ed75ca';

        $host = 'https://api.link.aliyun.com';
        $path = "/app/thing/event/timeline/get";
//        /app/thing/properties/get
        $params = '{
                        "id": "bded4128dc454a03b3d10c45de17b863",
                        "version": "1.0",
                        "request": {
                            "apiVer": "1.0.0"
                        },
                        "params": {
                            "productKey": "a1FMKlSx1Zj",
                            "deviceName": "0A17100617109259",
                            "identifier":"xxx",
                            "eventType":"info",
                            "start":1550290332,
                            "end":1552442752,
                            "pageSize":100,
                            "ordered":true
                        }
                    }';
        $demo = new Demo($appKey, $appSecret, $host);;
        $res = $demo->doPostString($path, $params);

        return $res;
    }

    public function getAppThingPropertyTimeline()
    {
        $appKey = '25934813';
        $appSecret = 'baf0238c94ed4111c1f2b9f102ed75ca';

        $host = 'https://api.link.aliyun.com';
        $path = "/app/thing/property/timeline/get";
        $params = '{
                        "id": "bded4128dc454a03b3d10c45de17b863",
                        "version": "1.0",
                        "request": {
                            "apiVer": "1.0.0"
                        },
                        "params": {
                            "productKey": "a1FMKlSx1Zj",
                            "deviceName": "0A17100617109259",
                            "identifier":"",
                            "start":1550290332,
                            "end":1552442752,
                            "pageSize":100,
                            "ordered":true
                        }
                    }';
        $demo = new Demo($appKey, $appSecret, $host);
//        echo '<pre/>';
//        return $params;
        $res = $demo->doPostString($path, $params);

        return $res;
    }

    // 调用阿里云应用托管api
    public function getApiThing(){
        $res = $this->getAppThingStatus();
        return $res;
    }

    //记录日志
    public function logWrite($fileName, $content){
        // die('123');
        $logDir = '../runtime/log';
        $now = date('Y-m-d');
        $nowDir = $logDir.'/'.$now;
        if(!is_dir($nowDir)){mkdir($nowDir, 0777, true);
        }
        $fileDir = $nowDir.'/'.$fileName;
        if(is_array($content)){
            $content = json_encode($content);
        }
        $fileContent = '在'.date('Y-m-d H:i:s').'时操作，内容为：'.$content;

        file_put_contents($fileDir, $fileContent."\n====================\n", FILE_APPEND);
    }

	function getcollectdata($dustdata)
	{
		//读取一条垃圾设备监测信息
		$startruntime=time();		//记录起始时间
		//jh_dustbin_info 垃圾桶信息表
		//jh_cap 设备信息表
		//jh_data 上报数据记录
		//jh_rubbish_record  垃圾数量数据记录
		//jh_overflow 垃圾溢出记录表
		//jh_recovery  回收记录表
		//$dustdata:distance---实测距离,template---温度,elec---电量,Signal---信号强度,install_height---安装高度
		//   imei---设备IMEI,imsi设备IMSI,dustbin_height---垃圾桶高度
		//   gather_time---采集时间,upload_time---上传时间,update_time---更新时间,updaterate---上报频率
		//   gps_gd---位置信息,data_type---数据来源（设备类型）,code---流水号
		//需要计算：rubbish_height---垃圾高度,dustnum---垃圾容量，last_height---最后一次高度
		//需要读取：last_dustnum---最后一次计算的垃圾数量(dustbin_dustnum)，dust_length---长，dust_width---宽
		//		dustbin_overflow---上次溢出状态（0未溢出，1溢出）,dustbin_lastgather---最后一次数据采集时间,cap_id--设备表id,dustbin_id---垃圾桶id
		//    last_data_id--上次采集数据的记录id,new_data_id--本次采集数据的记录id

		$code=$dustdata["imei"];			//流水号
		$this->writelog("开始处理数据：".$dustdata["originaldata"],$code);
		//1.读取基本信息
		$sql="select jc.cap_id,jbi.dust_length,jbi.dust_width,dustbin_dustnum,dustbin_overflow,dustbin_lastgather,dustbin_id ";
		$sql.=" from jh_cap jc join jh_dustbin_info jbi on jc.cap_id=jbi.cap_id ";
		$sql.=" where cap_imei='".$dustdata["imei"]."'";
		$result=Db::query($sql);

		if(!$result){return $this->returnerror("基础数据读取错误");}else{$this->writelog("读取基本信息完成",$code);}
		$row=$result[0];

		$dustdata["cap_id"]=$row["cap_id"];
		$dustdata["dustbin_id"]=$row["dustbin_id"];
		$dustdata["dust_length"]=$row["dust_length"];
		$dustdata["dust_width"]=$row["dust_width"];
		$dustdata["last_dustnum"]=$row["dustbin_dustnum"];
		$dustdata["dustbin_overflow"]=$row["dustbin_overflow"];
		$dustdata["dustbin_lastgather"]=$row["dustbin_lastgather"];
		
		//计算数据
		$dustdata["rubbish_height"]=$dustdata["install_height"]-$dustdata["distance"];
		$dustdata["dustnum"]=$dustdata["rubbish_height"]*$row["dust_width"]*$row["dust_length"];
		$dustdata["last_height"]=$row["dustbin_dustnum"]/($row["dust_width"]*$row["dust_length"]);

		//读取上次采集数据id
		$sql="select ifnull(max(data_id),0) id from jh_data where cap_id=".$row["cap_id"];
		$result=Db::query($sql);
		if(!$result){$dustdata["last_data_id"]=0;}else{$dustdata["last_data_id"]=$result[0]["id"];}

		

		
		//2.存入上报数据记录
		$dustdata["update_time"]=date('Y-m-d H:i:s');
		$sql="insert into jh_data(cap_imei,dustbin_id,cap_id,distance,dust_height,dustnum,template,electric,`signal`,`code`,";
		$sql.="data_from,gathertime,uploadtime,updatetime,state,unnormalinfo,last_data_id,overflow_id)values('";
		$sql.=$dustdata["imei"]."',".$dustdata["dustbin_id"].",".$dustdata["cap_id"];
		$sql.=",".$dustdata["distance"].",".$dustdata["rubbish_height"].",".$dustdata["dustnum"];
		$sql.=",".$dustdata["template"].",".$dustdata["elec"].",".$dustdata["Signal"];
		$sql.=",'".$dustdata["code"]."',".$dustdata["data_type"].",'".$dustdata["gather_time"];
		$sql.="','".$dustdata["upload_time"]."','".$dustdata["update_time"]."',0,'',".$dustdata["last_data_id"].",0)";

		$result=Db::execute($sql);

		if(!$result){return returnerror("上报数据保存错误");}else{$this->writelog("上报数据保存完成",$code);}

		//读取新纪录id
		$dustdata["new_data_id"]=Db::getLastInsID();

		//3.更新垃圾桶信息表
		$sql="update jh_dustbin_info set dustbin_dustnum=".$dustdata["dustnum"].",dustbin_lastgather='";
		$sql.=$dustdata["gather_time"]."' where dustbin_id=".$dustdata["dustbin_id"];
		$result=Db::execute($sql);
		if(!$result){return $this->returnerror("更新垃圾桶数据保存错误");}else{$this->writelog("更新垃圾桶数据保存完成",$code);}

		//4.垃圾溢出校验及数据处理
		//4.1 判断当前溢出状态:垃圾高度大于安装高度的90%则认为溢出
		$isoverflow=0;
		if($dustdata["rubbish_height"]/$dustdata["install_height"]>OVERRATE){
			$isoverflow=1;} 
		//如果之前是溢出状态那么垃圾高度只要大于50%则认为溢出
		if($dustdata["dustbin_overflow"]==1 && $dustdata["rubbish_height"]/$row["install_height"]>=OVERRATETORECYCLE){
			$isoverflow=1;} 
		//如果之前为溢出状态，读取之前的数据,保存在$overflowrow 中
		if($dustdata["dustbin_overflow"]==1){
			$sql="select overflow_id,overflow_time,overflow_num_time,overflow_dustnum from jh_overflow where dustbin_id=".$dustdata["dustbin_id"];
			$sql.=" and ifnull(recovery_id,0)=0 order by overflow_id desc limit 1";
			$result=Db::query($sql);
			$overflowrow=$result->fetch_assoc();
			if(!$overflowrow){
				//没有数据则更新为未溢出状态
				$dustdata["dustbin_overflow"]=0;
			}
		}
	
		//$dustperhour--估算每小时垃圾
		//$overflownum--溢出量
		//4.1.1 如果之前溢出，现在仍然溢出：估算溢出数量，更新最新溢出数据
		if($dustdata["dustbin_overflow"]==1 && $isoverflow==1){
			$dustperhour=calcdustnumperhour($dustdata["dustbin_id"],$dustdata["gather_time"],$db);	//估算每小时垃圾
			$overflownum=(strtotime($dustdata["gather_time"])-strtotime($overflowrow["overflow_num_time"]))/3600*$dustperhour;
			$sql="update jh_overflow set overflow_num_time='".$dustdata["gather_time"]."',overflow_dustnum=overflow_dustnum+";
			$sql.=$overflownum." where overflow_id=".$overflowrow["overflow_id"];
			$result=Db::execute($sql);
			
			//更新上报数据记录表，记录溢出id
			$sql="update jh_data set overflow_id=".$overflowrow["overflow_id"]." where data_id=".$dustdata["new_data_id"];
			$result=Db::execute($sql);
		}

	
		//4.1.2 如果之前未溢出，现在溢出：估算溢出数量，更新最新溢出数据
		if($dustdata["dustbin_overflow"]==0 && $isoverflow==1){
			$dustperhour=calcdustnumperhour($dustdata["dustbin_id"],$dustdata["gather_time"],$db);	//估算每小时垃圾
			$overflownum=(strtotime($dustdata["gather_time"])-strtotime($overflowrow["overflow_num_time"]))/3600*$dustperhour;
			if($dustdata["dustnum"]-$dustdata["last_dustnum"] < $overflownum){
				//估算垃圾量扣除采集数据的本次垃圾量作为溢出垃圾量
				$thisoverflownum=$overflownum-($dustdata["dustnum"]-$dustdata["last_dustnum"]);
			} else{
				$thisoverflownum==0;
			}
			//添加溢出记录
			$sql="insert into jh_overflow(dustbin_id,overflow_time,overflow_dustnum,overflow_num_time)values(";
			$sql.=$dustdata["dustbin_id"].",'".$dustdata["gather_time"]."',".$thisoverflownum.",".$thisoverflownum.")";
			$result=Db::execute($sql);
			$newoverflowid=mysqli_insert_id($db);
			//更新上报数据记录表，记录溢出id
			$sql="update jh_data set overflow_id=".$overflowrow["overflow_id"]." where data_id=".$dustdata["new_data_id"];
			$result=Db::execute($sql);
		}	
		//4.1.3 如果之前溢出，现在未溢出：溢出结束，关闭溢出记录---该部分在回收数据处理
	
		//4.1.4 如果前后都无溢出，则无需处理溢出
	
		//5.垃圾回收校验及数据处理
		//判断是否有回收：如果垃圾高度<安装高度的10%,并且减少的垃圾量>安装高度的10%
		  $isrecycle=0;
		if($dustdata["rubbish_height"]/$dustdata["install_height"]<RECYCLEHEIGHT && ($dustdata["last_height"]-$dustdata["rubbish_height"])/$dustdata["install_height"]>RECYCLEDIFF){
			$isrecycle=1;}
		//如果之前是溢出状态那么垃圾高度只要<50%则认为回收了
		if($dustdata["dustbin_overflow"]==1 && $dustdata["rubbish_height"]/$row["install_height"]<OVERRATETORECYCLE){
			$isrecycle=1;} 	
		if($isrecycle==1){
			//计算回收量
			//如果之前是溢出，则取之前的溢出量
			if($dustdata["dustbin_overflow"]==1){
				$recyclenum=$overflowrow["overflow_dustnum"];
			}else{
				$recyclenum=$dustdata["last_dustnum"]-$dustdata["dustnum"];
			}
			//添加回收记录
			$sql="insert into jh_recovery(dustbin_id,recovery_datetime,recovery_num)values(";
			$sql.=$dustdata["dustbin_id"].",'".$dustdata["gather_time"]."',".$recyclenum.")";
			$result=Db::execute($sql);;	
			//回收记录id
			$newrecoveryid=mysqli_insert_id($db);
			//接4.1.3 如果之前溢出，现在未溢出：溢出结束，关闭溢出记录
			if($dustdata["dustbin_overflow"]==1){
				$sql="update jh_overflow set overflow_recovery_time='".$dustdata["gather_time"]."',recovery_id=";
				$sql.=$newrecoveryid." where overflow_id=".$overflowrow["overflow_id"];
				$result=Db::execute($sql);
			}
		}

		
		//6.存入垃圾数量数据表
		if($isoverflow==1){
			//溢出状态
			$thisrubbishnum=$overflownum;
		} elseif($isrecycle!=1){
			//非回收状态，非溢出状态，即正常情况:本次垃圾量-上次垃圾量 
			$thisrubbishnum=$dustdata["dustnum"]-$dustdata["last_dustnum"];
		}
		//采集时间的小时值
		$gatherhour=intval(date("H",strtotime($dustdata["gather_time"])))+1;
		$sql="select ifnull(max(id),0) id from jh_rubbish_record where dustbin_id=".$dustdata["dustbin_id"];
		$sql.=" and dust_date='".date("Y-m-d",strtotime($dustdata["gather_time"]))."'";
		$result=Db::query($sql);
		$row=$result[0];
		if(!$row || $row["id"]==0){
			//没有数据则新增
			$sql="insert into jh_rubbish_record(dustbin_id,dust_date)values(".$dustdata["dustbin_id"];
			$sql.=",'".date("Y-m-d",strtotime($dustdata["gather_time"]))."')";
			$result=Db::execute($sql);
			$recordid=Db::getLastInsID();
		}else{
			$recordid=$row["id"];
		}

		//更新数据
		$sql="update jh_rubbish_record set dust_num=dust_num+".$thisrubbishnum.",dust_gcount=dust_gcount+1,";
		$sql.="dust_num".$gatherhour."=dust_num".$gatherhour."+".$thisrubbishnum.",dust_gcount";
		$sql.=$gatherhour."=dust_gcount".$gatherhour."+1 where dustbin_id=".$dustdata["dustbin_id"];
		$sql.=" and dust_date='".date("Y-m-d",strtotime($dustdata["gather_time"]))."'";
		$result=Db::execute($sql);

		//7.更新上报数据表相关字段状态
		$calctime=time()-$startruntime;
		$sql="update jh_data set state=1,last_data_id=".$dustdata["last_data_id"].",calctime=".$calctime;
		$sql.=" where data_id=".$dustdata["new_data_id"];
		$result=Db::execute($sql);

	echo "耗时：".($calctime);
		return true;
	
	
			
		foreach($dustdata as $k=>$v)
			{
				echo $k."&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;".$v."<br>";	
			}
	
	}
	
	function returnerror($info)
	{
		//读取垃圾监测信息出错
		die($info);
	}
	
	function writelog($info,$code)
	{
		//读取垃圾监测信息日志
		$this->logWrite(date("Ymd")."-collectdata.txt", $code."---".$info."\r\n");
	}
	
	function writelog2($info,$code)
	{
		//读取垃圾监测信息日志,通过自己的推送
		$this->logWrite(date("Ymd")."-collectdataself1.txt", $code."---".$info."\r\n");
	}
	
	function writeoverlog($info,$code)
	{
		//垃圾溢出报警日志
		$this->logWrite(date("Ymd")."-overflow.txt", $code."---".$info."\r\n");
	}
	
	function autogetinfoself()
	{
		//http://101.132.132.197/auto_get_infoself
		//通过自己的推送获取的推送（不通过阿里）
		$getinfo=json_encode($_REQUEST);
		$this->writelog2($getinfo,"");
		//echo $getinfo;
		$getinfo2=json_encode(file_get_contents('php://input'));
		$this->writelog2($getinfo2,"");
		//echo $getinfo2;
		echo "1";
	}
	
	function autogetinfo()
	{
		//http://101.132.132.197/auto_get_info
		//读取所有设备
		$sql="select jc.cap_imsi from jh_dustbin_info jdi join jh_cap jc on jdi.cap_id=jc.cap_id where dustbin_state=0 and cap_status=0";
		$result=Db::query($sql);
		//循环所有设备
		for($i=0;$i<count($result);$i++)
		{
			//调用getAppThingProperties接口
			$capdeviceName =$result[$i]["cap_imsi"];		//设备imei号
			$dust=$this->getAppThingProperties($capdeviceName);
			$dustres=json_decode($dust);
			if(isset($dustres->data[6])){
				$hexdata=$dustres->data[6]->value;	//读取lora传递的字符
				//echo $hexdata;
	
				$dustdata=$this->gethexinfo($hexdata,$capdeviceName);		//解析lora的字符串信息
				$this->getcollectdata($dustdata);
		  }
			//echo "<br>";		
		}
		//执行函数
		
		
	}
	
	//解析lora的字符串信息
	function gethexinfo($hexdata,$capdeviceName)
	{
		$dustdata["originaldata"]=$hexdata;		//原始数据
		$dustdata["distance"]=hexdec(substr($hexdata,2,2));		//实测距离
		$dustdata["template"]=hexdec(substr($hexdata,4,2));		//温度
		$dustdata["elec"]=hexdec(substr($hexdata,22,2));		//电量
		if (substr($hexdata,6,2) <> '00') {
				$longitude= $this->HexToCoordinate(substr($hexdata,6,8));
				$latitude= $this->HexToCoordinate(substr($hexdata,14,8));
				$dustdata["gps_gd"]= $latitude . "," . $longitude;		//位置信息
		}
		$dustdata["Signal"]=hexdec(substr($hexdata,40,4));		//信号强度
		$dustdata["install_height"]=hexdec(substr($hexdata,36,2));		//安装高度
		$dustdata["imei"]=$capdeviceName;		//设备IMEI
		$dustdata["imsi"]=$capdeviceName;		//设备IMSI
		//$dustdata["dustbin_height"]="110";		//垃圾桶高度
		$dustdata["gather_time"]=date('Y-m-d H:i:s');		//采集时间
		$dustdata["upload_time"]=date('Y-m-d H:i:s');		//上传时间
		$dustdata["update_time"]=date('Y-m-d H:i:s');		//更新时间
		$dustdata["updaterate"]=hexdec(substr($hexdata,32,2) . substr($hexdata,34,2));		//上报频率
		$dustdata["data_type"]="1";		//数据来源（设备类型）
		$dustdata["code"]=hexdec(substr($hexdata,14,4));		//流水号
		return $dustdata;
	}
		
	// 将 十六进制度分结构 转换成 标准坐标系格式
	function HexToCoordinate($inputHex = '')
	{
		$inputHex = hexdec($inputHex)/10000000;
		$degree = substr($inputHex,0,strpos($inputHex,'.'));
		$minute = substr($inputHex,strpos($inputHex,'.')+1);
		$minute = substr((substr($minute,0,2) . '.' . substr($minute,2))/60,2,8);
		$Coordinate = $degree . '.' . $minute;
		return $Coordinate;
	}
}
