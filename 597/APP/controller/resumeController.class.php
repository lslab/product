<?php

class resumeController extends baseController {

	public $site_id;
	public $name;
	public $surl;
	public $charset;
	public $usr;
	public $pwd;
	public $turl;

	public function __construct()
	{
		parent::__construct();

		$this->site_id = (int)$_GET['site_id'];
		if ($site=$this->site->findByPrk($this->site_id)) {
			$this->name=$site['name'];
			$this->surl=$site['surl'];
			$this->charset=$site['charset'];
			$this->usr=$site['usr'];
			$this->pwd=$site['pwd'];
			$this->turl=$site['turl'];
		} else {
			exit("վ��δ�ҵ� ID:".$this->site_id);
		}
	}//__construct(

	public function indexAction()
	{

		$this->contentAction();


	}//index


	public function login()
	{
		$data=array(
		'username'	=>	$this->usr,
		'password'	=>	$this->pwd,
		'usertype'	=>	'company',
		'imageField.x'	=>	'83',
		'imageField.y'	=>	'9'
		);
		$this->http->post($this->surl.'Public/login.shtml',$data);
		$body=$this->body();
		return strpos($body,'��¼�ɹ�')!==false;
	}//login
	
	public function contentAction()
	{
		$this->_getContent();
	}//recontentAction
	
	private function _getContent()
	{
		$resume=$this->resume->find(null,"site_id=$this->site_id and get=0");
		if (!$resume) {
			exit('�ɼ����');
		}
		
		$url=$this->surl."Person/Resume/Resume_Contact1.shtml?Param=".$resume['reid'];
		pp($url);
		$html=$this->get($url);
		
		$resumeRegx=new resumeRegex($this->site_id,$html);
		foreach ($resumeRegx->regex[0] as $tag=>$val) {
			$resume[$tag]=$resumeRegx->match($tag);
		}
		
		pp($resume);
		
	}//_getContent

	public function listAction()
	{
		$page=isset($_GET['page'])?(int)$_GET['page']:1;
		$pageCount=isset($_SESSION['pageCount']) ? $_SESSION['pageCount'] : 50;
		echo "<H2>���ڲɼ�������ַ</H2><PRE>��{$page}ҳ����{$pageCount}ҳ\n";

		$this->_getList($page);
		
		$page++;
		if ($page>$pageCount) {
			unset($_SESSION['pageCount']);
			exit('������ַ�ɼ����');
		} else {
			echo "<script>location=\"?r=site.relist&site_id={$this->site_id}&page=$page\";</script>";
		}
		
	}//list Action
	
	private function _getList($page)
	{
		$url=$this->surl."/Company/Company_Search_Quick.shtml?PageNo=$page&Psize=150";
//		pp($url,1);
		$html=$this->get($url);
		if (!isset($_SESSION['pageCount'])) {
			$pageCount=regMatch($html,'|��<Font color="#FF0000">(\d+)</Font>ҳ|i');
			$_SESSION['pageCount']=$pageCount;
		}
		preg_match_all('|\.\./Person/Resume/Resume_\d_(\d+)\.html|i',$html,$ar);
//		pp($ar,1);
		foreach ($ar[1] as $reid) {
			if (!$this->resume->findCount("reid='$reid' and site_id='$this->site_id'")) {
				$this->resume->create(array('site_id'=>$this->site_id, 'reid'=>$reid));
				echo "[����ID����ɹ�]\t$reid\n";
			} else {
				echo "[����ID�Ѿ�����]\t$reid\n";
			}
		}
	}//_getList



}//class