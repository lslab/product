<?php
Doo::loadModel('Url');
Doo::loadModel('Area');
Doo::loadController('BaseController');

class MainController extends BaseController {

	private $provinceCacheID = 'provinceCacheID';
	private $cityCacheID = 'cityCacheID';

	public function index()
	{
		echo "Hello World!00";
	}//index

	public function pages()
	{
		for ($i=0; $i<10; $i++) {
			$this->getpage();
		}
	}//pages

	public function getpage()
	{
		$mUrl=new Url();
		$mUrl->ok=0;
		$url=$mUrl->getOne();
		if (!$url) {
			exit('finished');
		}

		echo $url->id.$url->title."\n";

		$pageurl=$url->url;
		if (strpos(low($pageurl),'/show/')) {
			$pageurl = trim($pageurl,'/') . '/info/';
		}

		$file="e:/page/".$this->pinyins[$url->keyword]."/".$url->id.'.html';
		$html=$this->http()->get($pageurl);
		if (strpos($html,'请您输入验证码确认您是正常访问的用户')) {
			$imgcon=$this->http()->get('http://www.aibang.com/authimg.php?t='.mt_rand());
			$imgcode=imgCode($imgcon);
			$this->http()->get("http://www.aibang.com/?verifycode=$imgcode&area=common&cmd=validate&validate=1");
			$html=$this->http()->get($pageurl);
		}


		save($file,$html);

		$url->ok=1;
		$url->update();
	}//getpage

	public function getCity()
	{
		$citys = include(dirname(__FILE__).'/../config/citys.php');
		$provinces=array_keys($citys);
		$provinceID = (int)Doo::cache()->get($this->provinceCacheID);
		$cityID = (int)Doo::cache()->get($this->cityCacheID);
		if (isset($citys[$provinces[$provinceID]][$cityID])) {
			$nextCityID = $cityID+1;
			if (!isset($citys[$provinces[$provinceID]][$nextCityID])) {
				$this->cache()->set($this->provinceCacheID, $provinceID+1);
				$this->cache()->set($this->cityCacheID, 0);
			} else {
				Doo::cache()->set($this->cityCacheID,$nextCityID);
			}
			return array($provinces[$provinceID], $citys[$provinces[$provinceID]][$cityID]);
		} elseif (isset($provinces[$provinceID+1])) {
			$provinceID++;
			Doo::cache()->set($this->provinceCacheID,$provinceID);
			Doo::cache()->set($this->cityCacheID,0);
			return array($provinces[$provinceID], $citys[$provinces[$provinceID]][0]);
		} else {
			return false;
		}
	}//getCity

	public function geturl()
	{
		$page = (int)$this->cache()->get('page'); $page = $page>0 ? $page : 1;
		$keywordID=$this->cache()->get('keywordID');

		$mArea = new Area();
		$mArea->ok=0;
		$area = $mArea->limit();
		if (!$area) {
			$this->db()->query("update area set ok=0");
			$keywordID++;
			$this->cache()->set('keywordID',$keywordID);
			exit('某关键词完成');
		}

		$province = $area->province;
		$city = $area->city;
		$country = $area->country;

		$keywords = array('美食','咖啡厅','酒吧','茶馆','宾馆酒店','洗浴','足疗','按摩','KTV','夜总会','娱乐城','舞厅');
		$keywordID = $this->cache()->get('keywordID');
		if ($keywordID>=count($keywords)) {
			exit("所有关键词都完成");
		}
		$keyword=$keywords[$keywordID];

		$echo = "{$area->province},{$area->city},{$area->country}\tKeyword:$keyword\tPage:$page <p>\n\n";
		echo $echo;
		echo iconv('utf-8','gbk',$echo);

		$urls['美食']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=美食&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['咖啡厅']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=咖啡厅&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['酒吧']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=酒吧&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['茶馆']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=茶馆&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['宾馆酒店']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=宾馆酒店&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['洗浴']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=洗浴&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['足疗']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=足疗&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['按摩']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=按摩&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['KTV']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=ktv&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['夜总会']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=夜总会&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['娱乐城']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=娱乐城&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";
		$urls['舞厅']="http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city={$city}&a={$country}&q={$keyword}&as=5000&rc=1&ufcate=舞厅&apr=0|0&taste=&atmo=&h24=&tka=&disc=&staste=&aprice=&dp=&fd=&fm=&zone=&quan=&p={$page}";

		$url=$urls[$keyword];

		$html=$this->html($url);
		preg_match_all('~<a href="(http://www\.aibang\.com/(?:show|detail)/\d+-\d+)".*?>\s+<img src="(.*?)"(?: original="(.*?)")? alt="(.*?)"~',$html,$arUrls);

		//unset($arUrls[0]);	pp($arUrls,1);

		for ($i=0; $i<count($arUrls[0]); $i++) {
			$data=array(
			'url'=>$arUrls[1][$i],
			'titleimg'=>$arUrls[2][$i] ? $arUrls[2][$i] : $arUrls[3][$i],
			'title'=>$arUrls[4][$i],
			'city'=>$city,
			'keyword'=>$keyword,
			'province'=>$province,
			'country'=>$country,
			);

			if ( strlen($data['url']) && strlen($data['title']) && strlen($data['titleimg']) ) {
				$mUrl = new Url();
				$r_id = $mUrl->save($data);
				$mUrl->echoSaveResult($r_id, $data['title']);
			} else {
				pp($data);
			}
		}

		//
		$page++;
		if ($page>20 || !strpos($html,'>下一页</a>')) {
			$page=1;

			$area->ok=1;
			$area->update();
		}
		$this->cache()->set('page',$page);
		//		$url="?page=$page";
		//		append(dirname(__FILE__).'/../../urls.txt',"$url\n");
		//		echo "<script>location='$url';</script>";
	}//geturl

	public function pageList()
	{
		$mUrl = new Url();
		$mUrl->ok=0;
		$urls = $mUrl->find();
		for ($i=0; $i<count($urls); $i++) {
			echo md5($urls[$i]->url)."\t".$urls[$i]->url;
			echo "\n";
		}
	}//urlList

	public function getArea()
	{
		$citys=$this->getCity();
		$province=$citys[0];
		$city=$citys[1];

		$html=$this->html("http://www.aibang.com/?area=bizsearch2&cmd=bigmap&city=$city&a=&q=美食&as=5000&ufcate=&rc=1&zone=1&quan=&fm=&p=1");
		$subHtml = regMatch($html,'|>全市</a>(.*?)<div class="clear"></div>|is',false);
		$subHtml = str_ireplace('</a>',"===",$subHtml);
		$subHtml = strip_tags($subHtml);
		$subHtml = preg_replace('/\s/','',$subHtml);
		$subHtml = preg_replace('/\n|\r/','',$subHtml);
		$subHtml = trim($subHtml,'=');
		$_countrys = explode("===",$subHtml);
		$flag = false;
		foreach ($_countrys as $_country) {
			$_country = trim($_country);
			//			if (strlen($_country)) {
			$flag=true;
			$data = array(
			'province'=>$province,
			'city'=>$city,
			'country'=>$_country,
			);
			$mArea = new Area();
			$mArea->province=$province;
			$mArea->city=$city;
			$mArea->country=$_country;
			if (!$mArea->find()) {
				$mArea->insertAttributes($data);
			}
			//			}
		}

		if (!$flag) {
			append("g:/a.txt","$province,$city\n");
		}

		//
		echo "$province\t$city";
		$page=$_GET['page'];
		$page++;
		$url="?page=$page";
		echo "<script>location='$url';</script>";
	}//getArea
}
?>