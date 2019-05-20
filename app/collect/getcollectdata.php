<?php
//数据库
define("OVERRATE",0.9);	//溢出的鉴定比率，超过该比率认为溢出
define("OVERRATETORECYCLE",0.5);	//从溢出到回收的鉴定比率，在溢出状态高度低于这个认为是进行了回收，大于等于则保持溢出
define("RECYCLEHEIGHT",0.1);	//高度小于该比率，认为是回收的条件1
define("RECYCLEDIFF",0.1);	//2次采集相差高度大于该比率，认为是回收的条件2
$startruntime=time();		//记录起始时间
$db=new mysqli();
$db->connect("rm-uf6qd1p0j4dv12t59vo.mysql.rds.aliyuncs.com","recycle_admin","QWER!@#$%4321","recycle");
if($db->connect_errno){
	die();
}
$result=query_normal("set names 'utf8'",$db);

//收集垃圾信息,$dinfo为数组，用于接收采集的数据信息
function getcollectdata($dustdata,$db)
{
	//jh_dustbin_info 垃圾桶信息表
	//jh_cap 设备信息表
	//jh_data 上报数据记录
	//jh_rubbish_record  垃圾数量数据记录
	//jh_overflow 垃圾溢出记录表
	//jh_recovery  回收记录表
	//$dustdata:distance---实测高度,template---温度,elec---电量,Signal---信号强度,install_height---安装高度
	//   imei---设备IMEI,imsi设备IMSI,dustbin_height---垃圾桶高度
	//   gather_time---采集时间,upload_time---上传时间,update_time---更新时间,updaterate---上报频率
	//   gps_gd---位置信息,data_type---数据来源（设备类型）,code---流水号
	//需要计算：rubbish_height---垃圾高度,dustnum---垃圾容量，last_height---最后一次高度
	//需要读取：last_dustnum---最后一次计算的垃圾数量(dustbin_dustnum)，dust_length---长，dust_width---宽
	//		dustbin_overflow---上次溢出状态（0未溢出，1溢出）,dustbin_lastgather---最后一次数据采集时间,cap_id--设备表id,dustbin_id---垃圾桶id
	//    last_data_id--上次采集数据的记录id,new_data_id--本次采集数据的记录id

	//1.读取基本信息
	$sql="select jc.cap_id,jbi.dust_length,jbi.dust_width,dustbin_dustnum,dustbin_overflow,dustbin_lastgather,dustbin_id ";
	$sql.=" from jh_cap jc join jh_dustbin_info jbi on jc.cap_id=jbi.cap_id ";
	$sql.=" where cap_imei='".$dustdata["imei"]."'";
	$result=query_normal($sql,$db);
	$row=$result->fetch_assoc();
	if(!$row){return returnerror("基础数据读取错误");}
	$dustdata["cap_id"]=$row["cap_id"];
	$dustdata["dustbin_id"]=$row["dustbin_id"];
	$dustdata["dust_length"]=$row["dust_length"];
	$dustdata["dust_width"]=$row["dust_width"];
	$dustdata["last_dustnum"]=$row["dustbin_dustnum"];
	$dustdata["dustbin_overflow"]=$row["dustbin_overflow"];
	$dustdata["dustbin_lastgather"]=$row["dustbin_lastgather"];
	
	//计算数据
	$dustdata["rubbish_height"]=$row["install_height"]-$row["distance"];
	$dustdata["dustnum"]=$row["rubbish_height"]*$dustdata["dust_width"]*$dustdata["dust_height"];
	$dustdata["last_height"]=$dustdata["last_dustnum"]/($dustdata["dust_width"]*$dustdata["dust_height"]);
	
	//读取上次采集数据id
	$sql="select ifnull(max(data_id),0) id from jh_data where cap_id=".$row["cap_id"];
	$result=query_normal($sql,$db);
	$row=$result->fetch_assoc();
	if(!$row){$dustdata["last_data_id"]=0;}else{$dustdata["last_data_id"]=$row["id"];}
	
	//2.存入上报数据记录
	$sql="insert into jh_data(cap_imei,dustbin_id,cap_id,distance,dust_height,dustum,template,electric,signal,code,";
	$sql.="data_from,gathertime,uploadtime,updatetime,state,unnormalinfo,last_data_id,overflow_id)values('";
	$sql.=$dustdata["cap_imei"]."',".$dustdata["dustbin_id"].",".$dustdata["cap_id"];
	$sql.=",".$dustdata["distance"].",".$dustdata["dust_height"].",".$dustdata["dustnum"];
	$sql.=",".$dustdata["template"].",".$dustdata["electric"].",".$dustdata["sigal"];
	$sql.=",'".$dustdata["code"]."',".$dustdata["data_type"].",'".$dustdata["gather_time"];
	$sql.="','".$dustdata["upload_time"]."','".$dustdata["update_time"]"',0,'',".$dustdata["last_data_id"].",0)";
	$result=query_normal($sql,$db);
	$row=$result->fetch_assoc();
	if(!$row){return returnerror("上报数据保存错误");}
	
	//读取新纪录id
	$dustdata["new_data_id"]=mysqli_insert_id($db);
	
	//3.更新垃圾桶信息表
	$sql="update jh_dustbin_info set dustbin_dustnum=".$dustdata["dustnum"].",dustbin_lastgather='";
	$sql.=$dustdata["gather_time"]."' where dustbin_id=".$dustdata["dustbin_id"];
	$result=query_normal($sql,$db);
	$row=$result->fetch_assoc();
	if(!$row){return returnerror("更新垃圾桶数据保存错误");}
	
	//4.垃圾溢出校验及数据处理
	//4.1 判断当前溢出状态:垃圾高度大于安装高度的90%则认为溢出
	$isoverflow=0;
	if(dustdata["rubbish_height"]/$row["install_height"]>OVERRATE){
		$isoverflow=1;} 
	//如果之前是溢出状态那么垃圾高度只要大于50%则认为溢出
	if($dustdata["dustbin_overflow"]==1 && dustdata["rubbish_height"]/$row["install_height"]>=OVERRATETORECYCLE){
		$isoverflow=1;} 
	//如果之前为溢出状态，读取之前的数据,保存在$overflowrow 中
	if($dustdata["dustbin_overflow"]==1){
		$sql="select overflow_id,overflow_time,overflow_num_time,overflow_dustnum from jh_overflow where dustbin_id=".$dustdata["dustbin_id"];
		$sql.=" and ifnull(recovery_id,0)=0 order by overflow_id desc limit 1";
		$result=query_normal($sql,$db);
		$overflowrow=$result->fetch_assoc();
		if(!$overflowrow){
			//没有数据则更新为未溢出状态
			$dustdata["dustbin_overflow"]=0;
		}
	}
	//$dustperhour--估算每小时垃圾
	//$overflownum--溢出量
	//4.1.1 如果之前溢出，现在仍然溢出：估算溢出数量，更新最新溢出数据
	if($dustdata["dustbin_overflow"]==1 && isoverflow==1){
		$dustperhour=calcdustnumperhour($dustdata["dustbin_id"],$dustdata["gather_time"],$db);	//估算每小时垃圾
		$overflownum=(strtotime($dustdata["gather_time"])-strtotime($overflowrow["overflow_num_time"]))/3600*$dustperhour;
		$sql="update jh_overflow set overflow_num_time='".$dustdata["gather_time"]."',overflow_dustnum=overflow_dustnum+";
		$sql.=$overflownum." where overflow_id=".$overflowrow["overflow_id"];
		$result=query_normal($sql,$db);
		
		//更新上报数据记录表，记录溢出id
		$sql="update jh_data set overflow_id=".$overflowrow["overflow_id"]." where data_id=".$dustdata["new_data_id"];
		$result=query_normal($sql,$db);
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
		$result=query_normal($sql,$db);
		$newoverflowid=mysqli_insert_id($db);
		//更新上报数据记录表，记录溢出id
		$sql="update jh_data set overflow_id=".$overflowrow["overflow_id"]." where data_id=".$dustdata["new_data_id"];
		$result=query_normal($sql,$db);		
	}	
	//4.1.3 如果之前溢出，现在未溢出：溢出结束，关闭溢出记录---该部分在回收数据处理

	//4.1.4 如果前后都无溢出，则无需处理溢出
	
	//5.垃圾回收校验及数据处理
	//判断是否有回收：如果垃圾高度<安装高度的10%,并且减少的垃圾量>安装高度的10%
	$isrecycle=0;
	if(dustdata["rubbish_height"]/$row["install_height"]<RECYCLEHEIGHT && ($dustdata["last_height"]-$dustdata["rubbish_height"])/$row["install_height"]>RECYCLEDIFF){
		$isrecycle=1;}
	//如果之前是溢出状态那么垃圾高度只要<50%则认为回收了
	if($dustdata["dustbin_overflow"]==1 && dustdata["rubbish_height"]/$row["install_height"]<OVERRATETORECYCLE){
		$isrecycle=1;} 	
	if($isrecycle==1){
		//计算回收量
		//如果之前是溢出，则取之前的溢出量
		if($dustdata["dustbin_overflow"]==1){
			$recyclenum=overflowrow["overflow_dustnum"];
		}else{
			$recyclenum=$dustdata["last_dustnum"]-$dustdata["dustnum"];
		}
		//添加回收记录
		$sql="insert into jh_recovery(dustbin_id,recovery_datetime,recovery_num)values(";
		$sql.=$dustdata["dustbin_id"].",'".$dustdata["gather_time"],"',".$recyclenum.")";
		$result=query_normal($sql,$db);	
		//回收记录id
		$newrecoveryid=mysqli_insert_id($db);
		//接4.1.3 如果之前溢出，现在未溢出：溢出结束，关闭溢出记录
		if($dustdata["dustbin_overflow"]==1){
			$sql="update jh_overflow set overflow_recovery_time='".$dustdata["gather_time"]."',recovery_id=";
			$sql.=$newrecoveryid." where overflow_id=".$overflowrow["overflow_id"];
			$result=query_normal($sql,$db);
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
	$sql.=" and dust_date='".date("Y-m-d",strtotime($dustdata["gather_time"])."'";
	$result=query_normal($sql,$db);
	$row=$result->fetch_assoc();
	if(!$row || $row["id"]==0){
		//没有数据则新增
		$sql="insert into jh_rubbish_record(dustbin_id,dust_date)values(".$dustdata["dustbin_id"];
		$sql.=",'".date("Y-m-d",strtotime($dustdata["gather_time"])."')";
		$result=query_normal($sql,$db);
		$recordid=mysqli_insert_id($db);
	}else{
		$recordid=$row["id"];
	}
	//更新数据
	$sql="update jh_rubbish_record set dust_num=dust_num+".$thisrubbishnum.",dust_gcount=dust_gcount+1,";
	$sql.="dust_num".$gatherhour."=dust_num".$gatherhour."+".$thisrubbishnum.",dust_gcount";
	$sql.=$gatherhour."=dust_gcount".$gatherhour."+1 where dustbin_id=".$dustdata["dustbin_id"];
	$sql.=" and dust_date='".date("Y-m-d",strtotime($dustdata["gather_time"])."'";
	$result=query_normal($sql,$db);
	
	//7.更新上报数据表相关字段状态
	$calctime=time()-$startruntime;
	$sql="update jh_data set state=1,last_data_id=".$dustdata["last_data_id"].",calctime=".$calctime;
	$sql.=" where =".$dustdata["new_data_id"];
	$result=query_normal($sql,$db);
	return true;
}



function calcdustnumperhour($dustbin_id,$collectdate,$db)
{
	//计算前5天垃圾桶每小时的垃圾收集量，用于溢出时估算垃圾量
	$enddate=date("Y-m-d",strtotime($collectdate))
	$startdate=date("Y-m-d",strtotime("-5 day",strtotime($collectdate)));
	$sql="select ifnull(sum(dust_num)/count(id)/24,0) dustperhour from jh_rubbish_record where dustbin_id=".$dustbin_id;
	$sql.=" and dust_date>='".$startdate."' and dust_date<'".$enddate."'";
	$result=query_normal($sql,$db);
	$row=$result->fetch_assoc();
	if(!$row){return 0;}
	return $row["dustperhour"];
}

function returnerror($err)
{
	//生成错误时的返回信息
	$errorstring='{"success":"0","errorinfo","'.$err.'"}';
	return $errorstring;
	}

function query_normal($sql,&$db)
{
	$result=$db->query($sql);
	if(!$result)	
	{
		$err="Error ".$db->errno."   ".$db->error."\r\n sql:   ".$sql."\r\n";
		return 0;
	}
	else
	{
		return 1;
	}
}

}
?>