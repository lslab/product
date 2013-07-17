<?php
Doo::loadModel('Url');
Doo::loadModel('Area');
Doo::loadModel('Info');
Doo::loadController('BaseController');

class InfoController extends BaseController {

	public function img()
	{
		$page=(int)$_GET['page'];
		$pagesize=10000;
		$limitStart=$page*$pagesize;

		$mUrl=new Url();
		$Urls = $mUrl->find(array('limit'=>"$limitStart,$pagesize"));
		$Urls or exit('finish');

		$fo=fopen('e:/comments.txt','a');
		foreach ($Urls as $url) {
			fwrite($fo,"{$url->id}\t".$this->pinyins[$url->keyword]."\t{$url->url}/reviews/\n");
		}
		fclose($fo);

		echo $page;
		$page++;
		echo "<script>location='?page=$page';</script>";
	}//img

	public function index()
	{
		for ($i=0; $i<100; $i++)
		$this->Info();

		die;
		$page=(int)$_GET['page'];
		$pagesize=10000;
		$limitStart=$page*$pagesize;

		$mUrl=new Url();
		$mUrl->ok=0;
		//		$mUrl->id=331787;
		$Urls = $mUrl->find(array('limit'=>"$limitStart,$pagesize"));
		$Urls or exit('finish');

		foreach ($Urls as $Url) {
			$file = 'e:/page/'.$this->pinyins[$Url->keyword].'/'.$Url->id.'.html';
			if (file_exists($file)) {
				$Url->ok=1;
				$Url->update();
				echo $Url->id."\n";
			}
		}

		$page++;
		echo "<script>location='?page=$page';</script>";
	}

	public function Info()
	{
		$mUrl=new Url();
		$mUrl->ok=1;
		$mUrl->ok2=0;
		//		$mUrl->keyword='足疗';
		//				$mUrl->id=6363;
		$Url = $mUrl->getOne();
		$Url or exit("Get Url Finish!");

		echo "$Url->id ";
		//		pp($Url);

		//		echo "<a href='{$Url->url}' target='_blank'>{$Url->title}</a>";

		$file = 'e:/page/'.$this->pinyins[$Url->keyword].'/'.$Url->id.'.html';

		if (strpos($Url->url,'/detail/')) {
			$data = $this->getInfo($file);

			if ( !strlen($data['address']) ) {
				echo "<font color='red'>有字段未采集到</font>";
				pp($data);
				//失败
				$Url->ok2=2;
				$Url->update();
			} else {

				//				pp($data);//////////////////

				$data['id']=$Url->id;

				$this->db()->query("delete from info where id=".$Url->id);

				$mInfo = new Info();
				$mInfo->insertAttributes($data);

				//成功
				$Url->ok2=1;
				$Url->update();
			}
		} else {
			//跳过
			$Url->ok2=3;
			$Url->update();
		}


	}//Url

	//美食
	public function getInfo($file)
	{
		$data=array();
		$html=read($file);
		$data['address']=$this->getAddress($html);
		$data['tags']=$this->getTags($html);
		$data['tel']=$this->getTel($html);

		$intro=$this->getIntro($html);
		if (strpos($intro,'还没有店铺简介')!==false || strpos($intro,'暂无“商户概况”信息')!==false) {
			$data['intro']='';
		} else {
			$data['intro']=$intro;
		}

		$data['price']=$this->getPrice($html);
		$data['openhours']=$this->getOpenhours($html);
		if (strpos($data['openhours'],'添加')!==false) {
			$data['openhours']='';
		}

		return $data;
	}//getInfo_detail

	private function getAddress($html)
	{
		$r='';
		$r=regMatch($html,'|<dt>地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址：</dt>\s+<dd.*?>([^<>]+)|is');
		if(strlen($r)) return $r;
		//----------------------
		//http://www.aibang.com/detail/1541409155-1608492822
		$r=regMatch($html,'~地址：</div>\s+<div.*?>(.*?)<~is');
		if(strlen($r)) return $r;
		//----------------------
		$r=regMatch($html,"~<dt>地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址:</dt>\s+<dd>(.*?)&nbsp;~is");
		if(strlen($r)) return $r;

		//show----------------
		$r=regMatch($html,'~<th>地址</th>\s+<td>(.*?)<~is');
		if(strlen($r)) return $r;

	}//getAddress

	public function getTags($html)
	{
		$html=$this->commonReplace($html);

		$tmp=regMatch($html,'|<dt>标签：</dt>\s+<dd>(.+?)</dd>|is',false);
		$tmp=str_ireplace('</a>',',',$tmp);
		$tmp=strip_tags($tmp);
		$tmp=preg_replace('/[\s\n\r]/','',$tmp);
		$tags=trim($tmp,',');
		if (strlen($tags)) return $tags;

		//http://www.aibang.com/detail/1541409155-1608492822
		$tmp=regMatch($html,'|标签：</div>.*?<div.*?>(.+?)</div>|is',false);
		$tmp=str_ireplace('</a>',',',$tmp);
		$tmp=strip_tags($tmp);
		$tmp=preg_replace('/[\s\n\r]/','',$tmp);
		$tags=trim($tmp,',');
		if (strlen($tags)) return $tags;

	}//getTags

	private function getTel($html)
	{
		$html=$this->commonReplace($html);
		$r=regMatch($html,'~<dt>电话：</dt>\s+<dd class="fb">(.*?)</dd>~is');
		if (strlen($r)) return $r;

		//--------------------------
		$url='http://www.aibang.com'.regMatch($html,'~href="(/css13022816/Biz_1\.css\?v=.*?)"~i');
		$css = $this->cacheGet($url);
		preg_match_all('~(?:\.m_\d+,?)+\{display:([a-z]+)~i',$css,$arrString);
		$keys=array();
		for ($i=0; $i<count($arrString[0]); $i++) {
			$flag=low($arrString[1][$i]);
			preg_match_all('/\.m_(\d+)/i',$arrString[0][$i],$arrKey);
			foreach ($arrKey[1] as $key) {
				if ($flag=='none') {
					$keys[$key]='';
				} else {
					unset($keys[$key]);
				}
			}
		}
		$arrHide=array_keys($keys);

		$subHtml=regMatch($html,'|电话：</div>(.*?)</div>|is',false);
		foreach ($arrHide as $n) {
			$subHtml=preg_replace("/<span class='m_$n'>.*?<\/span>/is",'',$subHtml);
		}

		preg_match_all("/<span class='m_\d+'>(.*?)<\/span>/is",$subHtml,$arr);
		$r = trim(join('',$arr[1]));
		if (strlen($r)) return $r;

		//--------------------------------

		$url='http://www.aibang.com'.regMatch($html,'~href="(/css\d+/YshDetailIndex_\d+\.css)"~i');
		$css = $this->cacheGet($url);
		preg_match_all('~(?:\.m_\d+,?)+\{display:([a-z]+)~i',$css,$arrString);
		$keys=array();
		for ($i=0; $i<count($arrString[0]); $i++) {
			$flag=low($arrString[1][$i]);
			preg_match_all('/\.m_(\d+)/i',$arrString[0][$i],$arrKey);
			foreach ($arrKey[1] as $key) {
				if ($flag=='none') {
					$keys[$key]='';
				} else {
					unset($keys[$key]);
				}
			}
		}
		$arrHide=array_keys($keys);

		$subHtml=regMatch($html,'|<dd class="wth100 tel">(.*?)</dd>|is',false);
		foreach ($arrHide as $n) {
			$subHtml=preg_replace("/<span class='m_$n'>.*?<\/span>/is",'',$subHtml);
		}

		preg_match_all("/<span class='m_\d+'>(.*?)<\/span>/is",$subHtml,$arr);
		$r = trim(join('',$arr[1]));
		if (strlen($r)) return $r;
	}//getTel

	private function getIntro($html)
	{
		$r=regMatch($html,'~<div id="biz_desc_full".*?>(.*?)(?:<a\s|</div>)~is',false);
		if (strlen($r)) return $r;

	}//getIntro

	public function getPrice($html)
	{
		$r=regMatch($html,'~人均<span class="fb red">\d+</span>元~');
		if (strlen($r)) return $r;
		//-----------------------
		$r=regMatch($html,'~人均&emsp;<strong class="red"><span class="rmb">&yen;</span>(\d+)</strong>~is');
		if (strlen($r)) {
			$r="人均{$r}元";
			return $r;
		}
		//------------------------

		$r=regMatch($html,'~人均:<span class="rmb red">&yen;</span><label class="red">(.*?)</label>~');
		if ($r) {
			$r='人均'.$r.'元';
		}
		if (strlen($r)) return $r;

		//------------------------
		$r=regMatch($html,'|<p>每晚<span>&yen;(\d+)起</span></p>|is');
		if (strlen($r)) {
			$r="每晚{$r}元起";
		}
		if (strlen($r)) return $r;
		//---------------------------
		$r=regMatch($html,"~<dt>人均消费:</dt>\s+<dd>\d+元</dd>~is");
		$r=preg_replace('/[:\s\n\r]/','',$r);
		if (strlen($r)) return $r;
	}//getPrice

	private function getOpenhours($html)
	{
		$r=regMatch($html,'~营业时间：\s+</dt>\s+<dd>(.*?)<~is');
		if(strlen($r)) return $r;
		//------------------
		$r=regMatch($html,'~营业时间：</div>\s+<div.*?>(.*?)<~is');
		$r=preg_replace('/[\n\r]/',' ',$r);
		if(strlen($r)) return $r;
		//-----------------------------\
		$r=regMatch($html,"~<dt>营业时间:</dt>\s+<dd>(.*?)</dd>~is");
		if(strlen($r)) return $r;

		//show
		$r=regMatch($html,'~<th>营业时间</th>\s+<td>(.*?)<~is');
		if(strlen($r)) return $r;
	}//getOpenhours

	private function commonReplace($str)
	{
		$str=preg_replace('/&[a-z]{4};/i','',$str);
		return $str;
	}//commonReplace
}
?>