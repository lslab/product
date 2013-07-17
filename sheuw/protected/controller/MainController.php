<?php
Doo::loadModel('Member');
Doo::loadModel('m');
Doo::loadController('BaseController');

class MainController extends BaseController {

	private $logined = false;

	public function __construct()
	{
		parent::__construct();
		$this->http->setDefaultHeader('REFERER','http://www.sheuw.com/register/');
	}//__construct(

	public function xt()
	{
		for ($i=0; $i<20; $i++) {
			$this->_xt();
		}
	}//xt
	public function _xt()
	{
		$mMember=new Member();
		$member=$mMember->getByOK_first(0);
		//		$mMember->id='702658';$member=$mMember->getOne();//////////////aaaaaaaaaa
		$member or exit("sheuw finished ");
		pp($member);
		$member->ok=1;
		$member->update();
		echo $member->id."\t";//764998

		$file="e:/sheuw/".basename($member->img);
		$file2="e:/theuw-ok/".basename($member->img);
		if (file_exists($file)) {
			if (!is_file($file2)) {
				$img=new image();
				$info=$img->getImageInfo($file);
				$img->param($file)->thumb($file2,$info[0],$info[1]-24,2);
			} else {
				echo "$file2 exist\n";
			}

		} else {
			echo $member->img.' not exit\n';
		}


	}//xt

	public function index()
	{


		//		$mMember=new Member();
		//		$mMember->ok=2;
		//		$members=$mMember->find();
		//		$members or exit('ok');
		//		$mm=new m();
		//
		//		foreach ($members as $member) {
		//
		//			$m=$mm->getById_first($member->autoid);
		//
		//			$member->name=$m->name;
		//			$member->update();
		//
		//		}
		//		echo 'ok';
		//
		//		die;//////////////////
		$mMember=new Member();
		$mMember->ok=0;
		$mMember->imgok=mt_rand(0,1);
		$member=$mMember->getOne();
		//				$mMember->id='679233';$member=$mMember->getOne();//////////////aaaaaaaaaa
		$member or exit("sheuw finished ");
		//		pp($member);

		echo date("Y-m-d H:i:s\t");
		echo iconv('utf-8','gbk',$member->name) . "\n";

		$reg=$this->reg($member);
		if ($reg) {
			echo "registe\tok\n";
			$member->ok=1;

			$login=$this->login($member->qq.'@qq.com','pass1234');
			if ($login) {
				echo "login\tok\n";
				$this->http->get("http://www.sheuw.com/users/profile.asp?action=save&fieldvalue={$member->qq}&fieldname=rel_qq&typeid=2");
				$this->http->get("http://www.sheuw.com/users/profile.asp?action=save&fieldvalue={$member->tel}&fieldname=rel_mobile&typeid=1");
				$height=$this->height();
				$this->http->get("http://www.sheuw.com/users/profile.asp?action=save&fieldvalue=115&fieldname=vis_weight&typeid=0");
				$this->http->get($this->style());

				//usertags
				$tags=include(R.'tags.php');
				$_t=array();
				for ($i=0; $i<count($tags); $i++) {
					$_t[]="usertag=".urlencode($tags[$i]);
				}
				$postString = join("&",$_t);
				$this->http->post('http://www.sheuw.com/users/profile.asp?action=other&save=2',$postString,true);

				$img="E:/theuw-ok/{$member->id}".getFileExt($member->img);
				$_rand=mt_rand(0,1);
				if (file_exists($img)) {
					$html=$this->http->html('http://www.sheuw.com/users/avatar.asp');
					$data['UserID']=regMatch($html,'/name="UserID" type="hidden" value="(\d+)"/');
					$data['Title']='我的形象照';
					$data['ParentID']=0;
					$data['x']=49;
					$data['y']=20;
					//var_dump($data);
					$year=substr($member->date,0,4);
					$date=substr($member->date,4,4);
					$file=array(array('file1', $img));
					$this->http->post('http://www.sheuw.com/Common/upload.asp?ChannelID=1',$data,null,$file);
					$resp=$this->http->currentResponse();
					//			pp($resp);

					$html=$this->html('http://www.sheuw.com/users/avatar.asp');
					preg_match("~ctrlfile\('2','(\d+)','(\d+)'~i",$html,$t);
					$this->html("http://www.sheuw.com/users/avatar.asp?action=ctrl&useraction=2&parentid={$t[1]}&id={$t[2]}");
					$resp=$this->http->currentResponse();
					if (strpos($resp['body'],'操作成功')) {
						echo "setface\tok\n";
					} else {
						echo "setface\tfailed\n";
					}
				} else {
					echo "no face image\n";
				}
			} else {
				echo "login\tfailed\n";
			}
		} else {
			echo "registe\tfailed\n";
			$member->ok=2;
		}
		$member->update();

		echo "\n";

	}//index

	public function reg($member)
	{
		$citys=$this->citys();
		$data=array(
		'ChannelID'	=>	'2',
		'UserVouch'	=>	'0',
		'UserOrgan'	=>	'0',
		'regtype'	=>	'0',
		'emailname'	=>	$member->qq,
		'emaillist'	=>	'@qq.com',
		'useremail'	=>	$member->qq.'@qq.com',
		'usermobile'	=>	'',
		'mobilecode'	=>	'',
		'userpass'	=>	'pass1234',
		'password'	=>	'pass1234',
		'usernick'	=>	$member->name,
		'marriage'	=>	'0',
		'usersex'	=>	'1',
		'year'	=>	$member->y,
		'month'	=>	$member->m,
		'day'	=>	$member->d,
		'height'	=>	mt_rand(145,172),
		'education'	=>	mt_rand(1,6),
		'income'	=>	mt_rand(1,5),
		'province'	=>	$citys[0],
		'city'	=>	$citys[1],
		'area'	=>	'0',
		'usernote'	=>	'这家伙很懒，什么也没有留下',
		'mchar'	=>	'1000',
		'note_1'	=>	'',
		'note_2'	=>	'',
		'note_3'	=>	'',
		'note_4'	=>	'',
		'usertag'	=>	include(R.'tags.php'),
		'readme'	=>	'1',
		'x'	=>	'56',
		'y'	=>	'31'
		);
		//				var_dump($data);
		$this->http->post('http://www.sheuw.com/register/?action=save',$data);
		$resp=$this->http->getResponse();
		//		var_dump($resp);////////////////////////
		$r=strpos($resp['body'],'提交成功');
		if (!$r) {
			print_r(iconv('utf-8','gbk',trim(strip_tags($resp['body']))));
		}
		$member->ok= $r ? 1 : 2;
		$member->update();

		return $r;
	}//reg

	public function citys()
	{
		$array=array(11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64);
		$key=array_rand($array);
		$p=$array[$key];

		$cid=sprintf("%02d",mt_rand(1,9));
		return array($p,$p.$cid);
	}//citys

	public function login($usr,$pwd)
	{
		$data=array(
		'ChannelID'	=>	'2',
		'ltype'	=>	'1',
		'CookieDate'	=>	'1',
		'username'	=>	$usr,
		'password'	=>	$pwd,
		'logins'	=>	'登 录'
		);
		$this->http()->post('http://www.sheuw.com/login.asp?action=chk',$data);
		$resp = $this->http()->getResponse();
		//				var_dump($resp);///////////////////
		return strpos($resp['headers']['location'], 'action=ok');
	}//login

	public function height()
	{
		$array=array(40,45,50,55);
		$key=array_rand($array);
		return $array[$key];
	}//height

	public function style()
	{
		$array=array(
		'http://www.sheuw.com/users/profile.asp?action=save&fieldvalue=%u4E30%u6EE1&fieldname=vis_shape&typeid=6',
		'http://www.sheuw.com/users/profile.asp?action=save&fieldvalue=%u7626&fieldname=vis_shape&typeid=6',
		'http://www.sheuw.com/users/profile.asp?action=save&fieldvalue=%u8F83%u7626&fieldname=vis_shape&typeid=6',
		'http://www.sheuw.com/users/profile.asp?action=save&fieldvalue=%u5300%u79F0&fieldname=vis_shape&typeid=6',
		'http://www.sheuw.com/users/profile.asp?action=save&fieldvalue=%u82D7%u6761&fieldname=vis_shape&typeid=6',
		'http://www.sheuw.com/users/profile.asp?action=save&fieldvalue=%u9AD8%u6311&fieldname=vis_shape&typeid=6'
		);
		$key=array_rand($array);
		return $array[$key];
	}//style
}
?>