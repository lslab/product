<?php
Doo::loadModel('Url');
Doo::loadModel('Area');
Doo::loadModel('Info');
Doo::loadModel('Afcity');
Doo::loadModel('comment');
Doo::loadController('BaseController');

set_time_limit(300);
class PostController extends BaseController {
	public $logined=false;
	public $http;

	public function index()
	{
		for ($i=0; $i<30; $i++) 
			$this->post();
//		pp($this->db()->show_sql());
	}//index

	public function post()
	{
		if (!$this->logined) {
			$this->logined=$this->login();
		}
		
		$mInfo = new Info();
		$mInfo->ok=0;
		$Info = $mInfo->getOne();
		$Info or exit("Post Finish\n");

		$mUrl=new Url();
		$mUrl->id=$Info->id;
		$Url = $mUrl->getOne();

//		pp($Url);
//		pp($Info);
		
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
		'PicFile'	=>	'/img/'.$this->pinyins[$Url->keyword].'/'.$Url->id . getFileExt($Url->titleimg),
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
		
		foreach ($data as $k=>$v) {
			$data[$k]=mb_convert_encoding($v,'GBK','UTF-8');
		}
		$this->http()->post('http://www.afplay.com/leaf/Article_Edit.asp?action=add&Id=&ChannelID=1',$data);
		$resp=$this->http()->currentResponse();
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
		//省
		$marea=new Area();
		$mafcity=new Afcity();
		$rs=$marea->find();
		$mafcity->name=$city;
		$afCity=$mafcity->getone(array('where'=>"pid>0"));
		//市
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
			$afCounty=$mafcity->getOne();
		}

//		pp($afProvince);pp($afCity);pp($afCounty);

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
			return true;
		} else {
			return false;
		}

		var_dump($html);
		var_dump($this->http()->currentResponse());
	}//login
}
?>