<?php
Doo::loadModel('Member');
Doo::loadController('BaseController');

class MainController extends BaseController {

	private $logined = false;

	public function index()
	{
		echo '<a href="'.Doo::conf()->APP_URL.'list' . '">get list</a><br>';
		echo '<a href="'.Doo::conf()->APP_URL.'info' . '">get info</a><br>';
		echo '<a href="'.Doo::conf()->APP_URL.'post' . '">post</a>';
	}//index


	// ok值 0：未读取；1；成功；10；该会员设置了禁止任何人查看资料
	public function getinfo()
	{
		$page=isset($_GET['page'])?(int)$_GET['page']:1;

		$mMember=new Member();
		$member=$mMember->getOne(array('where'=>'ok=0', 'order'=>'id'));
		//		$member=$mMember->getById_first(1);////////////////////
		$member or exit("qingrenw get info finished! <a href='".Doo::conf()->APP_URL."post'>post</a>");

		$member->date=date("Ymd");

		$id=$member->id;
		$login=$this->login();
		if (!$login) {
			echo '登陆失败';
			echo "<script>location='?page=$page';</script>";
			exit;
		}
		$html=$this->html($member->url);
		if (strpos($html,'该会员设置了禁止任何人查看资料')) {
			$member->ok=10;
			$member->update();
			$page++;
			echo "<script>location='?page=$page';</script>";
			exit;
		}

		//省 市 县
		$subHtml = regMatch($html,'|<dt>现居住地：</dt><dd class="f">(.*?)</dd>|is');
		$citys = explode('&nbsp;&nbsp;',$subHtml);
		$member->city=trim($citys[1]);
		$member->county=trim($citys[2]);
		//		pp($citys,1);

		//头像
		$img=regMatch($html,'|<div class="bigPhoto"><a class="img"><img src="(http://image\.qingrenw\.com/.*?)"|');
		$tmp=low(basename($img));
		if ($tmp=='default_man.jpg' || $tmp=='default_girl.jpg') {
			$img='';
		} elseif($img) {
			$imgCon=$this->http->html($img);
			$imgpath="E:/img/qingrenw/$member->date";
			is_dir($imgpath) or mkdir($imgpath,777,true);
			save("$imgpath/Face{$member->id}".getFileExt($img), $imgCon);
		}
		$member->img=$img;
		//介绍
		$info=regMatch($html,'|<dt >昵称：</dt>.*?<dt>个性标签：</dt><dd class="tag">.*?</dd>|is',false);
		$info=preg_replace('|<dd class="sq_vip">诚信值:.*?</dd>|is','',$info);
		$info=preg_replace('|<span class="novip">.*?</span>|is','',$info);
		$info=preg_replace('|<\?dt\s*>|i','',$info);
		$info=preg_replace('|<dd[^>]*>|i','',$info);
		$info=preg_replace('|</dd>|i','<br>',$info);
		$info=strip_tags($info,'<br>');
		$info=r('玫瑰情人网','爱枫同城娱乐',$info);
		$info=r('qingrenw.com','afplay.com',$info);

		$info2=regMatch($html,'|<span>她的交友密语</span></dt><dd><ul>(.*?)</ul>|is',false);
		$info2=preg_replace('|<li>|i','',$info2);
		$info2=preg_replace('|</li>|i','<br>',$info2);
		$info2=strip_tags($info2,'<br>');
		$info2=r('玫瑰情人网','本站',$info2);

		$member->info=$info."<br>".$info2;
		//email
		$mid=regMatch($member->url,'|(\d+)\.html$|i');
		$posturl="http://www.qingrenw.com/Control/Opration.ashx?action=contact&looktype=1&heuserId=$mid";
		$html=$this->html($posturl);

		if (strpos($html,'与您的需求不符合') || strpos($html,'会员已被锁定') || strpos($html,'与您的需求不符合')) {
			$member->ok=10;
			$member->update();
			echo strip_tags($html);
			$page++;
			echo "<script>location='?page=$page';</script>";
			exit;
		}

		if (strpos($html,'每天限制查看100个')) {
			echo trim(preg_replace('/\s/','',strip_tags($html)));
			var_dump($member);
			exit;
		}

		file_exists('c:/qingrenw') or mkdir('c:/qingrenw',0777,true);
		save("c:/qingrenw/$member->id.txt",$html);
		$member->email=regMatch($html,"|><img  align='absmiddle' src='(.*?)'>|i");

		$emailpath="E:/img/qingrenw/".$member->date;
		is_dir($emailpath) or mkdir($emailpath,777,true);
		save($emailpath."/Email{$member->id}.jpg", $this->http->html($member->email));

		//qq
//		$member->qq=regMatch($html,"|<b>QQ：</b><img  align='absmiddle' src='(.*?)'>|i");
//		$qqlink = 'http://www.qingrenw.com' . regMatch($html,'|/goq\.ashx\?qq=[a-z0-9]+&m=link|i');
//		$this->html($qqlink);
//		$resp=$this->http->getResponse();
//		$member->realQQ=regMatch($resp['headers']['location'],'/uin=(\d+)/');
//
//		if (!$member->realQQ) {
//			$qqpath="E:/img/qingrenw/$member->date";
//			is_dir($qqpath) or mkdir($qqpath,777,true);
//			save($qqpath."/QQ{$member->id}.jpg", $this->http->html($member->qq));
//		}
		
		//qq
		$member->qq=regMatch($html,"|<b>QQ：</b><img  align='absmiddle' src='(.*?)'>|i");
		$qqpath="E:/img/qingrenw/$member->date";
		is_dir($qqpath) or mkdir($qqpath,777,true);
		save($qqpath."/QQ{$member->id}.jpg", $this->http->html($member->qq));		

		if (empty($member->email) && empty($member->qq)) {
			echo "email qq未采集到<br>";
			var_dump($html);
			var_dump($member);
			exit;
		}

		//								pp($member,9);///////////////////
		echo "（第 $page 次采集）\tID：$id\t$member->name\n";
		$member->ok=1;
		$member->update();

		$page++;
		echo "<script>location='?page=$page';</script>";
	}//_

	public function getlist()
	{
		//最近回访/最新注册
		//$order = 'LastLoginTime';
		$order = 'regdate';

		$mMember = new Member();

		$provinces=array("安徽","北京","福建","甘肃","广东","广西","贵州","海南","河北","河南","黑龙江","湖北","湖南","吉林","江苏","江西","辽宁","内蒙古","宁夏","青海","山东","山西","陕西","上海","四川","天津","西藏","新疆","云南","浙江","重庆","香港","澳门","台湾");
		$provinceID = isset($this->params['pid']) ? $this->params['pid'] : 0;
		$page = 1;
		$province = $provinces[$provinceID];
		$province_encode = urlencode($province);

		echo "<pre>".$provinceID."\t$province\tPage: $page\n\n";

		$url="http://www.qingrenw.com/user/search.aspx?sex=0&ddl_StartAge=18&ddl_EndAge=36&ddl_Province=$province_encode&ddl_City=%E5%9C%B0%E7%BA%A7%E5%B8%82&ddl_Area=%E5%8C%BA%E3%80%81%E5%8E%BF&order=$order&tb_user=%E6%98%B5%E7%A7%B0%E6%88%96%E8%81%94%E7%B3%BB%E6%96%B9%E5%BC%8F&recordcount=1500&page=$page";
		$html = $this->html($url);
		preg_match_all('|<img src="([^"]+)" alt="[^"]+" /></a></div><h4><a href="(http://www\.qingrenw\.com/user\d+/\d+\.html)" target="_blank">(.*?)</a>|i',$html,$arTmp);
		//		pp($arTmp);
		for ($i=0; $i<count($arTmp[0]); $i++) {
			$data=array(
			'name'	=>	$arTmp[3][$i],
			'img'	=>	'',
			'sex'	=>	'女',
			'age'	=>	'',
			'province'	=>	$province,
			'city'	=>	'',
			'county'=>	'',
			'info'	=>	'',
			'tel'	=>	'',
			'email'	=>	'',
			'qq'	=>	'',
			'realQQ'	=>	'',
			'date'=>	date("Ymd"),
			'url'	=>	$arTmp[2][$i],
			'ok'	=>	'0',
			);
			if ( !$mMember->getByUrl_first($arTmp[2][$i]) ) {
				$mMember->insertAttributes($data);
			}

			echo $data['name']."\n";
		}

		$provinceID++;
		if ($provinceID>=count($provinces)) {
			exit("qingrenw get list finished! <a href='".Doo::conf()->APP_URL."info'>info</a>");
		}
		$nexturl=Doo::conf()->APP_URL."list/$provinceID";
		echo "<script>location='$nexturl';</script>";
		//		pp($this->db()->showSQL());
	}//get list


	public function login()
	{
		echo "login...\n";
		$data=array(
		'UserName'	=>	'779745300@qq.com',
		'UserPwd'	=>	'123456cheng',
		'remember'	=>	'1',
		);
		$code=$this->http()->post('http://www.qingrenw.com/Control/CheckLogin.ashx?url=http%3A//www.qingrenw.com/',$data);
		//				$resp = $this->http()->getResponse();
		//				var_dump($resp);
		//		$this->logined = $resp['headers']['location']=='/my/';
		Doo::cache()->set('login.cookie',$this->http->getCookies());
		$this->logined = $code==302;
		return $code==302;
	}//login

	public function tmp()
	{
		$mMember=new Member();
		$member=$mMember->getByOK_first(0);
		//		$member=$mMember->getById_first(33);
		$member or exit("qingrwne get info finished\n");

		$mid=regMatch($member->url,'|(\d+)\.html$|i');
		$html=$this->html("http://www.qingrenw.com/Control/Opration.ashx?action=contact&looktype=1&heuserId=$mid");
		if ($html=='0') {
			$this->login();
			$html=$this->html("http://www.qingrenw.com/Control/Opration.ashx?action=contact&looktype=1&heuserId=$mid");
		}
		$member->email=regMatch($html,"|><img  align='absmiddle' src='(.*?)'>|i");
		$member->qq=regMatch($html,"|<b>QQ：</b><img  align='absmiddle' src='(.*?)'>|i");
		$qqlink = 'http://www.qingrenw.com' . regMatch($html,'|/goq\.ashx\?qq=[a-z0-9]+&m=link|i');
		//		$this->html($qqlink);
		//		$resp=$this->http->getResponse();
		//		pp($resp);
		//		$member->realQQ=regMatch($resp['headers']['location'],'/uin=(\d+)/');

		//		pp($member);///////////////////
		echo "$member->id\n";
		$member->ok=1;
		$member->update();
	}//tmp
}
?>