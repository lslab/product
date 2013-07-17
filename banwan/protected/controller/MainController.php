<?php
Doo::loadModel('Member');
Doo::loadController('BaseController');

class MainController extends BaseController {

	private $logined = false;

	public function index()
	{
		echo __FILE__;
	}//index

	public function get()
	{
		for ($i=0; $i<10; $i++)
		$this->info();
	}//get

	public function info()
	{
		$mMember = new Member();
		//		$mMember->id=397192; $member = $mMember->getOne();///////////
		$member = $mMember->getByOk_first(0);
		$member or exit('banwan get info finished ');

		$id = $member->id;
		echo "$id\t";
		$html = $this->getFile($id);
		$member->img = regMatch($html,'/javascript:go\("image\.html\?(.*?)"\)/');
		$member->tel = regMatch($html,'|<SPAN>手机号码：</SPAN>(.*?)</LI>|i');

		preg_match_all('|<UL id=OtherInfo>(.*?)</UL>|is',$html,$arrInfo);
		foreach ($arrInfo[1] as $_info) {
			$_info = str_ireplace('<sup><FONT CLASS="welcome">VIP</FONT></sup>','',$_info);
			$_info = preg_replace('|<LI id=MyInfo><SPAN>ＩＤ：</SPAN>.*?>投诉></A>\s*</LI>|is','',$_info);
			$_info = preg_replace('/<li.*?>/is','',$_info);
			$_info = preg_replace('/<\/li>/i','<br>',$_info);
			$info[]=$_info;
		}

		$info = join("<br>",$info);
		if (!strpos($html,'公开的联系方式')) {
			$vipHtml = read("e:/wanban/vip/$id.html");
			$subHtml = regMatch($vipHtml,'|(<TD align=right>类型：</TD>.*?)<TD align=right>上线IP：</TD>|is',false);
			$subHtml=preg_replace('/[\n\r]/','',$subHtml);
			$subHtml=preg_replace('/<\/TR>/i',"\n",$subHtml);
			$subHtml = trim(strip_tags($subHtml));
			$subHtml = r("\n",'<br>',$subHtml);

			$info .= '<br>'.$subHtml;

			if (!$member->tel) {
				$member->tel = regMatch($vipHtml,'|<TD align=right>手机/电话：</TD>\s*<TD>(.*?)</TD>|i');
			}
		}

		$member->info = $info;

		//		pp($member,9);
		$member->ok=1;
		$member->update();
	}//info

	public function getFile($id)
	{
		$mfile="E:/wanban/member/$id.html";
		if (!is_file($mfile)) {
			$this->login();
			return $this->http->html("http://me.banwan.com/member.asp?id=$id");
		} else {
			return read($mfile);
		}
	}//getFile

	public function getVipFile($id)
	{
		$mfile="E:/wanban/vip/$id.html";
		if (!is_file($mfile)) {
			$this->login();
			return $this->http->html("http://me.banwan.com/my/vip_contact.asp?id=$id");
		} else {
			return read($mfile);
		}
	}//getFile

	public function getMemberFile()
	{
		$cookie = getCache('login.cookie');
		if ($cookie) {
			$this->http->addCookies($cookie);
		} else {
			$this->login();
			setCache('login.cookie',$this->http->getCookies());
		}

		$mMember = new Member();
		$Member = $mMember->getByOk_first(0);
		if (!$Member) {
			exit("banwan download member file finished \n");
		}

		$id = $Member->id;
		$html = $this->http->html("http://me.banwan.com/member.asp?id=$id");
		$resp = $this->http->getResponse();
		if (strpos($resp['header']['location'], 'login.asp')) {
			Doo::cache()->flush('login.cookie');
			echo "not logined\n";
		} else {
			save("e:/wanban/member/$id.html",$html);
			if (!strpos($html,'公开的联系方式')) {
				$vipHtml = $this->http->html("http://me.banwan.com/my/vip_contact.asp?id=$id");
				save("e:/wanban/vip/$id.html",$vipHtml);
			}
			echo "$id ok\n";

			$Member->ok = 1;
			$Member->update();
		}

	}//getMemberFile
	
	//清除所有缓存
	public function clearCache()
	{
		Doo::cache()->flushAll();
	}//clearCache

	public function getlist()
	{
		$cookie = getCache('login.cookie');
		if ($cookie) {
			$this->http->addCookies($cookie);
		} else {
			$this->login();
			setCache('login.cookie',$this->http->getCookies());
		}

		$mMember = new Member();
		$page=getCache('page');
		$page = $page ? $page : 1;
		if ($page>100) {
			exit("列表完成");
		}
		echo "Page: $page\n\n";

		$url="http://me.banwan.com/searchresult.asp?currentpage=$page&sex=1&itype=1&chengyip=";
		$html = $this->http->html($url);
		$resp = $this->http->getResponse();
		if (strpos($resp['header']['location'], 'login.asp')) {
			$this->login();
			setCache('login.cookie',$this->http->getCookies());
			$html = $this->http->html($url);
		}

		preg_match_all('~<DIV class=menu_1><A HREF="member\.asp\?id=(\d+)" TARGET="_blank">(.*?)</A>.*?</DIV>\s+<DIV class=menu_2>.*?</DIV>\s+<DIV class=menu_2>(.*?)</DIV>\s+<DIV class=menu_2>(.*?)</DIV>\s+<DIV class=menu_2>.*?</DIV>\s+<DIV class=menu_2>(.*?)</DIV>\s+<DIV class=menu_1>(.*?)</DIV>~is',$html,$tmpArr);

		//		unset($tmpArr[0]);pp($tmpArr);
		for ($i=0; $i<count($tmpArr[1]); $i++) {
			$data=array(
			'id'	=>	$tmpArr[1][$i],
			'name'	=>	$tmpArr[2][$i],
			'img'	=>	'',
			'sex'	=>	'女',
			'age'	=>	$tmpArr[4][$i],
			'province'	=>	$tmpArr[5][$i],
			'city'	=>	$tmpArr[6][$i],
			'info'	=>	'',
			'tel'	=>	'',
			'ok'	=>	'0',
			'publish'	=>	'0',
			);

			if (!$mMember->getById($data['id'])) {
				//				pp($data,1);
				$mMember->insertAttributes($data);
				echo $data['name']."\t";
			}
		}

		echo "\n\n";
		//
		setCache('page', $page+1);
	}//getlist

	public function login()
	{
		if ($this->logined) {
			return true;
		}

		echo "login...\n";
		$data=array(
		'username'	=>	'cheng0911',
		'password'	=>	'123456cheng'
		);
		$this->http()->post('http://me.banwan.com/passport/loginsubmit.asp',$data);
		$resp = $this->http()->getResponse();
		//		var_dump($resp);
		$this->logined = $resp['headers']['location']=='/my/';
		return $this->logined;
	}//login

	public function img()
	{
		$fo=fopen(R.'img.txt','w');
		$mMember = new Member();
		$members=$mMember->find(array('select'=>'id,img,date'));
		foreach ($members as $member) {
			//			pp($member,1);
			if (strlen($member->img)) {
				$year = substr($member->date,0,4);
				$date = substr($member->date,4,4);
				$s="e:/img/banwan/$year/$date/".$member->id.getFileExt($member->img);
				if (file_exists($s)) {
					if (1635==filesize($s)) {
						unlink($s);
					} else {
						$member->imgok=1;
						$member->update();
					}
				}
				//				fwrite($fo, "$s\thttp://my.banwan.com".$member->img . "\n");
			}
		}
		fclose($fo);
		echo 'ok';
	}//img
}
?>