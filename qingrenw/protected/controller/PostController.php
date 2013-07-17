<?php
Doo::loadModel('Afcity');
Doo::loadModel('Member');
Doo::loadController('BaseController');

class PostController extends BaseController {
	public $logined=false;
	public $http;

	public function index()
	{

	}//index

	public function post()
	{
		if (!$this->logined) {
			$this->logined=$this->login();
		}

		$mmember = new member();
		$mmember->ok=1;
		$mmember->publish=0;
		//		$mmember->id=108;///////////////
		$member = $mmember->getOne();
		$member or exit("\nqingrenw Post Finish\n");

		$content=$member->info;
		$face="/img/qingrenw/$member->date/Face$member->id".getFileExt($member->img);
		if (is_file("e:{$face}")) {
			$content = "<center><img src=\"$face\" /></center><br>$content";
		}
		$qq="/img/qingrenw/$member->date/QQ$member->id.jpg";
		if ($member->realQQ) {
			$content.="<br>联系QQ：$member->realQQ<br>";
		} elseif (is_file("e:{$qq}")) {
			$content.="<br>联系QQ：<img src=\"$qq\"><br>";
		}
		$email="/img/qingrenw/$member->date/Email$member->id.jpg";
		if (file_exists("e:{$email}")) {
			$content.="<br>联系邮箱：<img src=\"$email\"><br>";
		}

		$citys=$this->getCity($member->province,$member->city,$member->county);
		$data = array(
		'Title'=>$member->name,
		'ClassID'=>43,
		'Province'=>$citys[0],
		'City'=>$citys[1],
		'County'=>$citys[2],
		'Description'	=>	'',
		'TitleColor'	=>	'',
		'TitleURL'	=>	'',
		'PicFile'	=>	$img,
		'Address'	=> '',
		'Opentime'	=>	'',
		'Lianxi'	=>	$member->tel,
		'Huanjing'	=>	'',
		'Price'	=>	'',
		'Fuwu'	=>	'',
		'Pingjia'	=>	'',
		'IsPass'	=>	'1',
		//		'vSaleJifen'	=>	'',
		'IsPic'	=>	'',
		'IsDelete'	=>	'',
		'ComeFrom'	=>	'爱枫同城娱乐网',
		'Author'	=>	'爱枫同城娱乐网',
		'AddTime'	=>	date("Y-m-d H:i:s"),
		'Hits'	=>	'0',
		'Content'	=> $content,
		'action_ok'	=>	'add',
		'IsUserAdd'	=>	'',
		'GiveJifen'	=>	'',
		'Inputer'	=>	'',
		'InputerId'	=>	'',
		'bntSubmit'	=>	' 立即保存 ',
		//出售 2 个爱币
		'IsSale'=>1,
		'vSaleJifen'=>2,
		);

		//				pp($data);
		foreach ($data as $k=>$v) {
			$data[$k]=mb_convert_encoding($v,'GBK','UTF-8');
		}

		$this->http()->post('http://www.afplay.com/leaf/Article_Edit.asp?action=add&Id=&ChannelID=1',$data);
		$resp=$this->http()->currentResponse();
		pp($resp);
		$lastid = trim($resp['body']);
		if (preg_match('/^\d+$/',$lastid)) {
			$member->publish=1;
			$member->update();
		} else {
			$member->publish=2;
			$member->update();
			pp($data,1);
		}

		$page=isset($_GET['page']) ? $_GET['page'] : 1;
		echo "第 $page 次发布\t".$member->id.date("\tY-m-d H:i:s\t").$member->name."\n";
		$page++;
		echo "<script>location='?page=$page';</script>";
	}//



	public function getCity($province, $city, $country)
	{
		$mAfcity = new Afcity();
		if ($country) {
			$objCountry = $mAfcity->getOne(array('where'=>"name like '%$country%'"));
		}
		if ($country && $objCountry) {
			if ($objCountry) {
				$objCity=$mAfcity->getById_first($objCountry->pid);
				$objProvince=$mAfcity->getById_first($objCity->pid);
			}
		} else {
			$objCity=$mAfcity->getOne(array('where'=>"name='$city' and pid>0"));
			if ($objCity) {
				$objProvince = $mAfcity->getById_first($objCity->pid);
				$objCountry = $mAfcity->getOne(array('where'=>"pid={$objCity->id}",'order'=>'rand()'));
			}
		}

		//随机
		if (!$objProvince->id || !$objCity->id || !$objCountry->id) {
			$objProvince=$mAfcity->getOne(array('where'=>"pid=0", 'order'=>'rand()'));
			$objCity=$mAfcity->getOne(array("where"=>"pid=$objProvince->id","order"=>"rand()"));
			$objCountry=$mAfcity->getOne(array("where"=>"pid=$objCity->id","order"=>"rand()"));
		}

		//				pp($this->db()->showSQL());////////////////////////
		//		var_dump($objProvince);var_dump($objCity);var_dump($objCountry);die;
		return array($objProvince->id, $objCity->id, $objCountry->id);
	}//getCity

	public function login()
	{
		$data=array(
		'username'=>'admin',
		'userpwd'=>'admin',
		'action'=>'login',
		'getcode'=>'1111',
		);

		$code = $this->http()->post('http://www.afplay.com/leaf/ad_login.asp', $data);
		$resp=$this->http()->currentResponse();
		if ($code==302 && strpos($resp['headers']['location'], 'ad_index.html')!==false) {
			//			Doo::cache()->set('login.cookie',$this->http()->getCookies());
			$this->logined=true;
			return true;
		} else {
			$this->logined=false;
			return false;
		}

		var_dump($html);
		var_dump($this->http()->currentResponse());
	}//login
}
?>