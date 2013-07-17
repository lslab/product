<?php

class postController extends baseController {

	public $tmp;
	public $logDir;

	public function indexAction()
	{
		pp(date("Y-m-d H:i:s"));
		if (!$this->login()) {
			pp("��½ʧ��");
			//			append('g:/a.txt',$this->tmp."\n");///////////////////
		} else {
			pp('��½�ɹ�');
			
			$this->logDir=APP_PATH.'/log/';
			if (!file_exists($this->logDir)) {
				mkdir($this->logDir,777,true);
			}

			if ($subject=$this->subject->find(null,'ok<4')) {
				$this->postSubject($subject);
			} elseif ($reply=$this->reply->find(null,'ok<4','ord')) {
				$this->postReply($reply);
			} else {
				pp('���⡢�ظ�ȫ���������',1);
			}
		}

		echo '<script>location="?r=post";</script>';

	}

	public function postSubject($subject)
	{
		$forumid=$this->getForumID($subject['tid']);
		$html=$this->http->getHtml('http://www.0555hs.com/forum.php');
		$formhash = regMatch($html,'/formhash=([a-z0-9]+)/i');
		$postData = array(
		'typeid'	=>	$this->getTypeID($forumid),
		'subject'	=>	$subject['title'],
		'message'	=>	$subject['content'],
		'formhash'	=>	$formhash,
		'usesig'	=>	'1',
		'posttime'	=>	time()
		);

		//		var_dump($postData);
		$this->http->post("http://www.0555hs.com/forum.php?mod=post&action=newthread&fid=$forumid&topicsubmit=yes&infloat=yes&handlekey=fastnewpost&inajax=1",$postData);
		$response=$this->http->getBody();
		$postid=(int)regMatch($response,'/thread-(\d+)-\d+-\d+\.html/');
		if ($postid) {
			$subject['postid']=$postid;
			$subject['ok']=4;
			$msg='���� �����ɹ�';
			pp($msg);
		} else {
			$subject['ok']+=1;
			$msg='���� ����ʧ��';
			pp($msg);
		}
		
		append($this->logDir.'post_'.date("Y-m-d").'.txt',date("Y-m-d H:i:s")."\n$msg\n$response\n\n");
		pp($subject);
		$this->subject->save($subject);
	}//postSubject

	public function postReply($reply)
	{
		$postid = $this->subject->findField('postid', "fid={$reply['fid']}");
		if ($postid==0) {
			$this->db()->query("update reply set ok=5 where fid={$reply['fid']}");
			pp('�ظ�ʧ�ܣ�ԭ�򣺸ûظ���Ӧ���ⷢ��ʧ��');
		} else {
			$forumid=$this->getForumID($reply['tid']);
			$html=$this->http->getHtml('http://www.0555hs.com/forum.php');
			$formhash = regMatch($html,'/formhash=([a-z0-9]+)/i');

			$replyUrl="http://www.0555hs.com/forum.php?mod=post&action=reply&fid={$forumid}&tid={$postid}&extra=page%3D1&replysubmit=yes&infloat=yes&handlekey=fastpost&inajax=1";
			$replyData=array(
			'message'	=>	$reply['content'],
			'posttime'	=>	time(),
			'formhash'	=>	$formhash,
			'usesig'	=>	'',
			'subject'	=>	'  '
			);
			$this->http->post($replyUrl,$replyData);
			$response=$this->http->getBody();
			if (strpos($response,'�ظ������ɹ�')) {
				$msg='�ظ��ɹ�';
				pp($reply['content']);
				$reply['ok']=4;
			} else {
				$reply['ok']+=1;
				$msg='�ظ�ʧ��';
				pp($response);
			}
			append($this->logDir.'reply_'.date("Y-m-d").'.txt',date("Y-m-d H:i:s")."\n$msg\n$response\n\n");
			$this->reply->save($reply);
		}

	}//postReply

	public function getForumID($tid)
	{
		$array=array(
		25=>109,
		17=>43,
		13=>43,
		14=>36,
		);
		return $array[$tid];
	}//getForumID

	//fid���Լ���̳��FID
	public function getTypeID($fid)
	{
		$array=array(
		43=>64,
		109=>89,
		36=>4
		);
		return $array[$fid];////

		$a=$array[$fid];
		$key=array_rand($a);
		return $a[$key];
	}//getTypeID

	public function login()
	{
		$rooturl='http://www.0555hs.com/';

		$html=$this->http->gethtml($rooturl.'member.php?mod=logging&action=login');
		$formhash = regMatch($html,'/name="formhash" value="(.*?)"/i');
		$loginhash = regMatch($html,'/loginhash=([a-z0-9]+)/i');
		$data=array(
		'formhash'	=>	$formhash,
		'referer'	=>	$rooturl.'forum.php',
		'loginfield'	=>	'username',
		'username'	=>	$this->getUser(),
		'password'	=> '08280828',
		'questionid'	=>	'0',
		'answer'	=>	'',
		'cookietime'	=>	'2592000',
		'loginsubmit'	=>	'true'
		);

		$this->tmp=$data['username'];/////////////////////////
		//var_dump($data);var_dump($loginhash);

		$this->http->post("{$rooturl}member.php?mod=logging&action=login&loginsubmit=yes&loginhash=$loginhash&inajax=1",$data);
		$html=$this->http->getBody();
		if (strpos($html, '��ӭ������')!==false) {
			return true;
		} else {
			pp($data);
			pp(strip_tags($html));
			return false;
		}
	}//login

	public function getUser()
	{
		$users=include(APP_PATH.'/common/users.php');
		$key=array_rand($users);
		return $users[$key];
	}//getUser
}


?>