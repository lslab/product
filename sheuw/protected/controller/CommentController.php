<?php
Doo::loadModel('Url');
Doo::loadModel('Area');
Doo::loadModel('Info');
Doo::loadModel('comment');
Doo::loadController('BaseController');

set_time_limit(60);
class CommentController extends BaseController {

	public function index()
	{
		for ($i=0; $i<100; $i++) {
			$this->getData();
		}
	}//index

	public function getData()
	{
		$mUrl=new Url();
		$mComment=new Comment();

		$mUrl->comment_ok=0;
		//		$mUrl->id=61;
		//		$mUrl->keyword='舞厅';
		$Url = $mUrl->getOne(array('order'=>'id'));
		$Url or exit("get comment data from file finish\n");

		//pp($Url);/////////////////////////////

		$file='e:/comment2/'.$this->pinyins[$Url->keyword].'/'.$Url->id.'.html';

		if (is_file($file)) {
			$html=read($file);

			preg_match_all('|<div[^>]+id="review_content_\d+">(.+?)</div>|is',$html,$arrComments);
			if (empty($arrComments[1])) {
				preg_match_all('|<span class="star_s_rate\s+star_s\d+">.*?<dd>(.+?)</dd>|is',$html,$arrComments);
			}

			preg_match_all('|<a href="/user/profile/\d+".*?>(.+?)</a>|is',$html,$arrUsers);
			preg_match_all('|<div class="rk_uname">\s+<span>(.+?)</span>(.+?)</span>|is',$html,$arrUsers2);
			foreach ($arrUsers[1] as $_user) {
				$_user=strip_tags($_user);
				$_user=preg_replace('/\s/','',$_user);
				$users[]=$_user;
			}
			foreach ($arrUsers2[1] as $_user) {
				$_user=strip_tags($_user);
				$_user=preg_replace('/\s/','',$_user);
				$users[]=$_user;
			}

			foreach ($arrComments[1] as $_comment) {
				$_comment = preg_replace('/<span>.*?<\/span>/is','',$_comment);
				$_comment = preg_replace('/\s/','',$_comment);
				$_comment = preg_replace('/&[a-z]{3-4};/i','',$_comment);
				$_comment = preg_replace('/<br\s*\/?>/i',"\n",$_comment);
				$comments[]=$_comment;
			}

			for ($i=0; $i<count($comments); $i++) {
				$data=array(
				'name'=>$users[$i],
				'content'=>$comments[$i],
				'url_id'=>$Url->id,
				);
				if (empty($data['name'])) {
					$data['name']='匿名';
				}
				$mComment->insertAttributes($data);
			}


		}

		$Url->comment_ok=1;
		$Url->update();

		echo $Url->id . "\n";
	}//getData

	function comment()
	{
		$mUrl=new Url();
		$mUrl->comment_ok=0;
		$Url=$mUrl->getOne();
		if (!$Url) {
			exit('finished');
		}

		$bid=basename($Url->url);
		$page=1;
		$flag=false;

		do {
			echo "ID: $Url->id\tpage: $page\n";

			$data=array(
			'bid'=>$bid,
			'page'=>$page,
			'cur_uid'=>'false',
			'remarkSortType'=>'1',
			'reviewType'=>'0',
			'tag'=>'sys_review_user',
			'pn'=>'15',
			'frm'=>'allreview'
			);

			$html = $this->http()->post('http://www.aibang.com/?area=biz&cmd=bremark',$data);
			$file = "e:/comment/".$this->pinyins[$Url->keyword]."/{$Url->id}-{$page}.html";
			save($file, $html);

			$flag = strpos($html,'>下一页</a>');
			$page++;

		} while ($flag);

		//
		$Url->comment_ok=1;
		$Url->update();

	}//
}
?>