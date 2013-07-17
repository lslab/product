<?php
include(APP_PATH.'/class/sinaEditor.class.php');

class configController extends baseController {

	public function indexAction()
	{
		if (empty($_POST)) {
			$data=(array)Cache::get('config');
			$formset=Form::formSet($data);
			$editor=new sinaEditor('intro');
			$editor->Value=$data['intro'];
			$editor->BasePath='./js';
//			$editor->Height=600;
//			$editor->Width=800;
			$editor->AutoSave=false;
			include($this->template());
		} else {
			$data=array_map('trim',$_POST);
			$data['rooturl']=rtrim($data['rooturl'],'/').'/';
			Cache::save('config',$data);
			$this->redirect('?r=config',1,'配置保存成功！');
		}
	}


}

