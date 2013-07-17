<?php

class listController extends baseController {

	public function indexAction()
	{
		pp('开始采集列表……');
		$url=$this->currUrl();
		$html=$this->http->getHtml($url);

		preg_match_all('/forum_view\.php\?tid=(\d+)&amp;fid=(\d+)/i',$html,$ar);
		pp($ar[0]);
		
		for ($i=0; $i<count($ar[0]); $i++) {
			$tid=$ar[1][$i];
			$fid=$ar[2][$i];

			if ($this->list->findByPrk($fid)) {
				$this->list->save(array('tid'=>$tid,'fid'=>$fid,'ok'=>0));
			} else {
				$this->list->create(array('tid'=>$tid,'fid'=>$fid,'ok'=>0));
			}
		}
		
		if($this->nextUrl()) {
			$url='?r=list';
		} else {
			$url='?r=content';
			echo '列表采集完成';
		}
		echo "<script>location=\"$url\";</script>";
	}

	public function currUrl()
	{
		$pageOrd=(int)Cache::get('pageOrd');
		$tidOrd=(int)Cache::get('tidOrd');
		$tids=include(APP_PATH.'/common/tids.php');

		$tid=$tids[$tidOrd];
		$startPage=$pageOrd*40;
		return "http://www.ahhs.gov.cn/forum/forum_list.php?tid=$tid&keyword=&StartPage=$startPage";

	}//currListUrl

	public function nextUrl()
	{
		$maxPage=3;////////////////
		$pageOrd=(int)Cache::get('pageOrd');
		$tidOrd=(int)Cache::get('tidOrd');
		$tids=include(APP_PATH.'/common/tids.php');

		$pageOrd++;
		Cache::save('pageOrd',$pageOrd);
		if ($pageOrd>=$maxPage) {
			$pageOrd=0;
			Cache::save('pageOrd',$pageOrd);

			$tidOrd++;
			Cache::save('tidOrd',$tidOrd);
			if ($tidOrd>=count($tids)) {
				$tidOrd=0;
				Cache::save('tidOrd',$tidOrd);

				return false;
			}
		}

		return true;
	}//nextUrl
}


?>