<?php

class mainController extends baseController {

	public function indexAction()
	{
		include($this->template());
		echo $this->expendTime();
	}
}


?>