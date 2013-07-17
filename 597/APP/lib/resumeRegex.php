<?php
class resumeRegex {
	public $site_id;
	public $html;
	public $regex;

	public function __construct($site_id,$html)
	{
		$this->site_id=$site_id;
		$this->html=$html;
		/////////////////////////////////////////////
		$regex[0]['姓名']	= '~>姓　　名：</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['性别']	= '~<td width="80" bgcolor="#FFFFFF">性　　别：</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['民族']	= '~>民　　族：</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['出生年月']	= '~<td width="80" bgcolor="#FFFFFF">出生年月：</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['证件号码']	= '~>证件号码：</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['婚姻状况']	= '~<td width="80" bgcolor="#FFFFFF">婚姻状况：</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['身高']	= '~>身　　高：</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['体重']	= '~<td width="80" bgcolor="#FFFFFF">体　　重：</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['户籍']	= '~>户　　籍：</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['现所在地']	= '~<td width="80" bgcolor="#FFFFFF">现所在地：</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['毕业学校']	= '~>毕业学校：</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['学历']	= '~<td width="80" bgcolor="#FFFFFF">学　　历：</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['专业名称']	= '~>专业名称：</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['毕业年份']	= '~<td width="80" bgcolor="#FFFFFF">毕业年份：</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['工作经验']	= '~>工作经验：</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['最高职称']	= '~<td width="80" bgcolor="#FFFFFF">最高职称：</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		//$regex[0]['个人相片']	=
		$regex[0]['职位性质']	= '~<td width="80">职位性质：</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['职位类别']	= '~<td width="80">职位类别：</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['职位名称']	= '~>职位名称：</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['工作地区']	= '~工作地区：</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['待遇要求']	= '~待遇要求：</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['到职时间']	= '~到职时间：</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['教育培训']	= '~>教育培训</td>(.*?)</table>is~';
		$regex[0]['工作经历']	= '~工作经历</td>(.*?)其他信息</td>is~';
		$regex[0]['自我评价']	= '~自我评价：</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['手机号码']	= '~手机号码：</td>\s+<td width="240" class="jl">\s+<img src="/AspNet/StrToImg\.ashx\?type=code&email=(.*?)"~';
		//$regex[0]['联系电话']	=
		$regex[0]['电子邮件']	= '~电子邮件：</td>\s+<td width="240" class="jl">\s+<img src="/AspNet/StrToImg\.ashx\?type=email&email=(.*?)"~';
		$regex[0]['QQ']			= '~tencent://message/\?uin=(\d+)~';
		//$regex[0]['个人网站']	=
		$regex[0]['邮政编码']	= '~邮政编码：</td>\s+<td class="jl">(\d+)</td>~';
		$regex[0]['通讯地址']	= '~通讯地址：</td>\s+<td colspan="3" class="jl">(.*?)</td>~';

		$this->regex=$regex;
	}//__construct(

	public function match($tag)
	{
		//		pp($tag);
		if (isset($this->regex[$this->site_id][$tag])) {
			$val=regMatch($this->html,$this->regex[$this->site_id][$tag]);
		} else {
			$val=regMatch($this->html,$this->regex[0][$tag]);
		}

		$method_0=$tag.'_0';
		$method_x=$tag.'_'.$this->site_id;
		if (method_exists($this, $method_x)) {
			$val = $this->$method_x($val);
		} elseif (method_exists($this, $method_x)) {
			$val = $this->$method_0($val);
		}

		return $val;
	}//match

	public function 职位性质_0($s)
	{
		return preg_replace('/\s/','',$s);
	}//职位性质_0

}
