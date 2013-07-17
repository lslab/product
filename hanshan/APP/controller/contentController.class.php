<?php
//http://www.ahhs.gov.cn/forum/forum_view.php?fid=37836&tid=16&StartPage=90
class contentController extends baseController {

	public function indexAction()
	{
		$list=$this->list->findByOK(0);
		if (!$list) {
			echo "采集完毕";
			exit('<script>location="?r=post";</script>');
		}
		pp('开始采集内容……');

		$currPage=$list['currpage'];
		$url="http://www.ahhs.gov.cn/forum/forum_view.php?tid={$list['tid']}&fid={$list['fid']}&StartPage={$list['currpage']}";
		pp($url);
		$html=$this->http->getHtml($url);

		//lastPage
		$_lastPage=regMatch($html,'~StartPage=(\d+)"\s+class="p_redirect">&rsaquo;\|</a>~i');
		if ($_lastPage) {
			$list['lastpage']=$_lastPage;
		}

		preg_match_all('/<div id="content">(.+?)<\/div>/is',$html,$arContent);
		pp($arContent[1]);///////////////////////
		$subject=$this->subject->findByPrk($list['fid']);
		if (!$subject) {
			$title=regMatch($html, '|</div><b>标题：(.+?)</b></div>|i');
			$content=$this->clearContent($arContent[1][0]);

			$this->subject->create(array('fid'=>$list['fid'], 'tid'=>$list['tid'], 'title'=>$title, 'content'=>$content));
			append(APP_PATH.'/log/subject_'.date("Y-m-d").'.txt',date("Y-m-d H:i:s")."\n$title\n$url\n\n");
		}

		preg_match_all('|<b>\s+(\d+)\s+楼</b>|i', $html, $arOrd);
		for ($i=1; $i<count($arContent[0]); $i++) {
			$replyData=array(
			'fid'=>$list['fid'],
			'tid'=>$list['tid'],
			'ord'=>$arOrd[1][$i-1],
			'content'=>$this->clearContent($arContent[1][$i]),
			);
			//			pp($replyData);////////////////////
			if (!$this->reply->find(null,array('fid'=>$list['fid'],'ord'=>$replyData['ord']))) {
				$this->reply->save($replyData);
				append(APP_PATH.'/log/reply_'.date("Y-m-d").'.txt',date("Y-m-d H:i:s")."\n$title\n$url\n第 {$replyData['ord']} 楼\n{$replyData['content']}\n\n");
			}
		}

		//
		$currPage+=30;
		if ($list['lastpage']>0) {
			if ($currPage<=$list['lastpage']) {
				$list['currpage']=$currPage;
			}
		}
		if ($currPage>$list['lastpage']) {
			$list['ok']=1;
		}
		$this->list->save($list);

		echo '<script>location="?r=content";</script>';
	}

	public function clearContent($c)
	{
		$c=preg_replace('/[\r\n]/','',$c);
		$c=preg_replace("|<br\s*/?>|i","\n",$c);
		$c=preg_replace('/&[a-z]{1,8};/i','',$c);
		$c=strip_tags($c);
		$c=trim($c);
		return $c;
	}//clearContent
}


