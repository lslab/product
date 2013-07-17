<?php
Doo::loadModel('Afcity');
Doo::loadModel('Member');
Doo::loadController('BaseController');

class PostController extends BaseController {
	public $logined=false;
	public $http;

	public function index()
	{

		for ($i=0; $i<50; $i++)
		$this->post();
//				pp($this->db()->show_sql());
	}//index

	public function post()
	{
		if (!$this->logined) {
			$this->logined=$this->login();
		}

		$mmember = new member();
		$mmember->publish=0;
//		$mmember->id=780662;
		$member = $mmember->getOne();
		$member or exit("Post Finish\n");

		$year = substr($member->date,0,4);
		$date = substr($member->date,4,4);
		$img="/img/banwan/$year/$date/".$member->id.getFileExt($member->img);

		$content = "<center><img src=\"$img\" /></center><br>".$member->info;
		
		$citys=$this->getCity($member->province,$member->city);
		$data = array(
		'Title'=>$member->name,
		'ClassID'=>42,
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
		'vSaleJifen'	=>	'',
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
		//出售 8 个爱币
		'IsSale'=>1,
		'vSaleJifen'=>8,
		);
		
//		pp($data);

		$this->http()->post('http://www.afplay.com/leaf/Article_Edit.asp?action=add&Id=&ChannelID=1',$data);
		$resp=$this->http()->currentResponse();
//		pp($resp);
		$lastid = trim($resp['body']);

		echo $member->id.date("\tY-m-d H:i:s\t").$data['Title']."\n";
		$member->publish=1;
		$member->update();

	}//



	public function getCity($province, $city)
	{
		list($city,$country) = explode('/',preg_replace('/\s/','',$city));
		//省
		$mAfcity = new Afcity();
		if ($country) {
			$objCountry = $mAfcity->getByName_first($country);
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
		
//		var_dump($objCity);
//		var_dump($objProvince);
//		var_dump($objCountry);

		return array($objProvince->id, $objCity->id, $objCountry->id);
	}//getCity

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
			Doo::cache()->set('login.cookie',$this->http()->getCookies());
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