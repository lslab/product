<?php
Doo::loadController('BaseController');

class PostController extends BaseController {
	public $logined=false;
	public $http;

	public function index()
	{
				for ($i=0; $i<10; $i++)
		$this->post();
		//		pp($this->db()->show_sql());
		
//		echo "<script>location='?';</script>";
	}//index

	public function post()
	{
		if (!$this->logined) {
			$this->logined=$this->login();
		}

		$mImg = new Img();
		$mThread = new Thread();

		$mImg->ok=0;
		$FirstImg = $mImg->getOne();
		$tid = $FirstImg->tid;
		$FirstImg or exit("BBS img post Finish!\n");
		
		//copy("E:/www.fangcaoting.com/wwwroot/data/attachment/forum/img/".$FirstImg->attachment,"e:/img/bbsthumb/$tid".getFileExt($FirstImg->attachment));

		$mThread->tid = $tid;
		$Thread = $mThread->getOne();

		$mImg->ok=0;
		$mImg->tid=$tid;
		$Imgs = $mImg->find();
		foreach ($Imgs as $Img) {
			$content.='<p align="center"><img alt="" src="/img/bbs/' . $Img->attachment . '" /></p>'."\n";
		}

		$subject = preg_replace('/\[\s*\d+\s*p\s*\]/i','',$Thread->subject);
		$subject = trim($subject);

		$data = array(
		'Title'=>$subject,
		'ClassID'=>37,
		'Province'=>0,
		'City'=>0,
		'County'=>0,
		'Description'	=>	'',
		'TitleColor'	=>	'',
		'TitleURL'	=>	'',
		'PicFile'	=>	'/img/bbsthumb/'.$tid . getFileExt($Img->attachment),
		'Address'	=> '',
		'Opentime'	=>	'',
		'Lianxi'	=>	'',
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
		'bntSubmit'	=>	' 立即保存 '
		);

//		pp($data,1);
		foreach ($data as $k=>$v) {
			$data[$k]=mb_convert_encoding($v,'GBK','UTF-8');
		}
		$this->http()->post('http://www.afplay.com/leaf/Article_Edit.asp?action=add&Id=&ChannelID=1',$data);
				$resp=$this->http()->currentResponse();
//				var_dump($resp);
				$lastid = trim($resp['body']);


		$this->db()->query("update img set ok=1 where tid=$tid");
		echo $Thread->tid."\tOK\n";
		
	}//

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