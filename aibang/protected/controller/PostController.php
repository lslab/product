<?php
Doo::loadModel('Url');
Doo::loadModel('Area');
Doo::loadModel('Info');
Doo::loadModel('Afcity');
Doo::loadModel('comment');
Doo::loadController('BaseController');

class PostController extends BaseController {
	public $logined=false;
	public $http;

	public function index()
	{
		//		myRedis::delete('id');
		//		var_dump(myRedis::get('id'));die;
		for ($i=0; $i<10; $i++) {
			$this->post();
		}
		echo "\n";
		//		$mInfo = new Info();
		//		$mInfo->ok=0;
		//		$Infos = $mInfo->find(array('select'=>'id'));
		//		foreach ($Infos as $info) {
		//			myRedis::set('id',$info->id);
		//		}
		//
		//		echo 'ok';
	}//index

	public function postComment()
	{
		$data=array(
		'Content'	=>	'1111111111',
		'UserName'	=>	'22222',
		'TableID'	=>	'366981',
		'TypeID'	=>	'2',
		'sID'	=>	'354869771'
		);
		$posturl='http://www.afplay.com/inc/pls.asp';
	}//postComment

	public function post()
	{
		if (!$this->logined) {
			$this->logined=$this->login();
		}

		$mInfo = new Info();
		//				$mInfo->ord=mt_rand(0,11);
		//		$mInfo->ord=$this->getOrd();
		$mInfo->ok=0;
		//						$mInfo->id=myRedis::get('id');
		$Info = $mInfo->getOne();
		//		var_dump($Info);die;
		//		print_r($this->db()->showSQL());die;
		if (!$Info) {
			$this->delOrd($mInfo->ord);
			exit("Post Finish\n");
		}


		$mUrl=new Url();
		$mUrl->id=$Info->id;
		$Url = $mUrl->getOne();

		//				pp($Url);
		//				pp($Info);
		if (in_array($Url->keyword,array('洗浴','宾馆酒店','KTV','足疗','按摩','夜总会','娱乐城','舞厅'))) {
			$img='';
		} else {
			$img='/img/'.$this->pinyins[$Url->keyword].'/'.$Url->id . getFileExt($Url->titleimg);
		}

		$pcc=$this->getPCC($Url->city,$Url->country);
		$data = array(
		'Title'=>$Url->title,
		'ClassID'=>$this->getCate($Url->keyword),
		'Province'=>$pcc[0],
		'City'=>$pcc[1],
		'County'=>$pcc[2],
		'Description'	=>	'',
		'TitleColor'	=>	'',
		'TitleURL'	=>	'',
		'PicFile'	=>	$img,
		'Address'	=> $Info->address,
		'Opentime'	=>	$Info->openhours,
		'Lianxi'	=>	$Info->tel,
		'Huanjing'	=>	'',
		'Price'	=>	$Info->price,
		'Fuwu'	=>	$Info->tags,
		'Pingjia'	=>	'',
		'IsPass'	=>	'1',
		'vSaleJifen'	=>	'',
		'IsPic'	=>	'',
		'IsDelete'	=>	'',
		'ComeFrom'	=>	'爱枫同城娱乐网',
		'Author'	=>	'爱枫同城娱乐网',
		'AddTime'	=>	date("Y-m-d H:i:s"),
		'Hits'	=>	'0',
		'Content'	=> $this->content($Info->intro),
		'action_ok'	=>	'add',
		'IsUserAdd'	=>	'',
		'GiveJifen'	=>	'',
		'Inputer'	=>	'',
		'InputerId'	=>	'',
		'bntSubmit'	=>	' 立即保存 '
		);
		//pp($data,1);////////////////////////////
		foreach ($data as $k=>$v) {
			$data[$k]=mb_convert_encoding($v,'GBK','UTF-8');
		}
		$this->http()->post('http://www.afplay.com/leaf/Article_Edit.asp?action=add&Id=&ChannelID=1',$data);
		$resp=$this->http()->currentResponse();
		//		pp($resp);

		$lastid = trim($resp['body']);

		$mInfo2 = new Info();
		$mInfo2->id2=$lastid;
		if ($lastid && !$mInfo2->getOne()) {
			$Info->ok=1;
			$Info->id2=$lastid;
			echo $Info->id."\t".$data['Title']."\tOK\n";
		} else {
			$Info->ok=2;
			echo $Info->id."\t".$data['Title']."\tFALSE\n";
		}

		$Info->ord=100;
		$Info->update();
	}//

	public function getAfLastID()
	{
		$html=$this->http()->html('http://www.afplay.com/leaf/Article_List.asp?ChannelId=1');
		$id = regMatch($html,'|<input name="Id" type="checkbox" id="Id" value="(\d+)"|');
		return $id;
	}//getAfLastID

	public function getPCC($city,$country)
	{
		//		pp($city);pp($country);
		//市
		$marea=new Area();
		$mafcity=new Afcity();
		$rs=$marea->find();
		$mafcity->name=$city;
		$afCity=$mafcity->getone(array('where'=>"pid>0"));
		//省
		$mafcity=new Afcity();
		$mafcity->id=$afCity->pid;
		$afProvince=$mafcity->getOne();
		//县
		$mafcity=new Afcity();
		$mafcity->pid=$afCity->id;
		$mafcity->name=$country;
		$afCounty=$mafcity->getOne();
		if (!$afCounty) {
			$mafcity=new Afcity();
			$mafcity->pid=$afCity->id;
			$afCounty=$mafcity->getOne(array('order'=>'rand()'));
		}

		//				pp($afProvince);pp($afCity);pp($afCounty);
		//				pp($this->db()->showSQL());

		return array($afProvince->id,$afCity->id,$afCounty->id);
	}//getPcc

	public function getCate($keyword)
	{
		$r=array(
		'美食' => 32,
		'咖啡厅' => 32,
		'酒吧' => 32,
		'茶馆' => 32,
		'宾馆酒店' => 33,
		'洗浴' => 18,
		'足疗' => 18,
		'按摩' => 18,
		'KTV' => 36,
		'夜总会' => 36,
		'娱乐城' => 36,
		'舞厅' => 36,
		);
		return $r[$keyword];
	}//getCate

	public function content($s)
	{
		$s=preg_replace('|<br\s*/?>|i',"\n",$s);
		return strip_tags($s);
	}//content

	public function login($force=false)
	{
		echo "login...\n";
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
			echo "login ok\n";
			return true;
		} else {
			echo "login failse\n";
			return false;
		}

		var_dump($html);
		var_dump($this->http()->currentResponse());
	}

	public function _cookieLogin()
	{
		echo "use cookie login...\n";
		$cookie=Doo::cache()->get('login.cookie');

		if ($cookie) {
			$this->http()->addCookies($cookie);
			return true;
		} else {
			return $this->_login();
		}
	}//_cookieLogin
	public function _login()
	{
		echo "login...\n";
		$data=array(
		'username'=>'admin',
		'userpwd'=>'admin',
		'action'=>'login',
		'getcode'=>'1111',
		);

		$code = $this->http()->post('http://www.afplay.com/leaf/ad_login.asp', $data);
		$resp=$this->http()->currentResponse();
		//		pp($resp);
		if ($code==302 && strpos($resp['headers']['location'], 'ad_index.html')!==false) {
			//			Doo::cache()->set('login.cookie',$this->http()->getCookies());
			return true;
		} else {
			return false;
		}

		var_dump($html);
		var_dump($this->http()->currentResponse());
	}//_login

	public function getOrd()
	{
		$arrOrd=Doo::cache()->get('ord');
		//		var_dump($arrOrd);
		if (!$arrOrd) {
			exit('finished');
			$array[0]='';
			$array[1]='';
			$array[2]='';
			$array[3]='';
			$array[4]='';
			$array[5]='';
			$array[6]='';
			$array[7]='';
			$array[8]='';
			$array[9]='';
			$array[10]='';
			$array[11]='';
			$arrOrd=$array;
			Doo::cache()->set('ord',$arrOrd);
		}
		return array_rand($arrOrd);
	}//getOrd

	public function delOrd($key)
	{
		$arrOrd=Doo::cache()->get('ord');
		unset($arrOrd[$key]);
		Doo::cache()->set('ord',$arrOrd);
	}//delOrd
}
?>