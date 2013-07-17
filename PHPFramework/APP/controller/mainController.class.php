<?php

class mainController extends Controller {

	protected function _beforAction()
	{
		//$this->fullPageCache();
	}

	public function indexAction()
	{
//		include(APP_PATH.'/config/route.php');
//		$router=new Router();
//		$result=$router->execute($route);
//		
//		pp($result);

//		die;////////////////////////////////////////
		if (!$time=Cache::get('time')) {
			$time=date("Y-m-d H:i:s");
			Cache::save($time);
		}

		$title='this is my title';
		$name='gujie';

		$mMember=$this->model('member');
//		$mMember->save(array('uid'=>1,'password'=>date("Y-m-d H:i:s")));
		
		$members=$mMember->findAll();
		
//		$this->db()->query("update __TABLE_PREFIX__members set password='".time()."' where uid=2");

		
//		$this->view()->forceCompile=true;
		
		require($this->template());
	}
	
	public function showAction()
	{
		$uid=(int)$_GET['uid'];
		$memeber=$this->model('member')->findByPrk($uid);
		pp($memeber);
		
		$this->model('member')->debug=true;
		$s=$this->model('member')->findField('username',array('uid'=>$uid,'username'=>$memeber['username']));
		pp($s);
		
	}//showAction
	
	public function editAction()
	{
		$uid=(int)$_GET['uid'];
		$mMember=new memberModel();
		$mMember->debug=true;
		
		if (empty($_POST)) {
			$member=$mMember->findByPrk($uid);
			$formset=Form::formSet($member);
			$formerror=Form::formError($_SESSION['formerror']);
			
			unset($_SESSION['formset'],$_SESSION['formerror']);
			include($this->template());
		} else {
			$data=$_POST;
			$error=$mMember->validate($data,'all_one');
			if (!$error) {
				$mMember->save($_POST);
				pp($_POST);
//				$this->redirect($_SERVER['HTTP_REFERER'],1,'编辑成功');
			} else {
				$_SESSION['formerror']=$error;
				$this->redirect($_SERVER['HTTP_REFERER']);
			}
			
		}
		
	}//editAction
	
	public function deleteAction()
	{
		$uid=(int)$_GET['uid'];
		$this->model('member')->delByPrk($uid);
		$this->redirect($_SERVER['HTTP_REFERER'],1,'删除成功');
	}//deleteAction
	
	public function addAction()
	{
		if (empty($_POST)) {
			include($this->template());
		} else {
			
		}
	}//addAction
}


?>