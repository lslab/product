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
		$regex[0]['����']	= '~>�ա�������</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['�Ա�']	= '~<td width="80" bgcolor="#FFFFFF">�ԡ�����</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['����']	= '~>�񡡡��壺</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['��������']	= '~<td width="80" bgcolor="#FFFFFF">�������£�</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['֤������']	= '~>֤�����룺</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['����״��']	= '~<td width="80" bgcolor="#FFFFFF">����״����</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['���']	= '~>�����ߣ�</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['����']	= '~<td width="80" bgcolor="#FFFFFF">�塡���أ�</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['����']	= '~>����������</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['�����ڵ�']	= '~<td width="80" bgcolor="#FFFFFF">�����ڵأ�</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['��ҵѧУ']	= '~>��ҵѧУ��</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['ѧ��']	= '~<td width="80" bgcolor="#FFFFFF">ѧ��������</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['רҵ����']	= '~>רҵ���ƣ�</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['��ҵ���']	= '~<td width="80" bgcolor="#FFFFFF">��ҵ��ݣ�</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['��������']	= '~>�������飺</td>\s+<td width="170" bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		$regex[0]['���ְ��']	= '~<td width="80" bgcolor="#FFFFFF">���ְ�ƣ�</td>\s+<td bgcolor="#FFFFFF" class="jl">(.*?)</td>~';
		//$regex[0]['������Ƭ']	=
		$regex[0]['ְλ����']	= '~<td width="80">ְλ���ʣ�</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['ְλ���']	= '~<td width="80">ְλ���</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['ְλ����']	= '~>ְλ���ƣ�</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['��������']	= '~����������</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['����Ҫ��']	= '~����Ҫ��</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['��ְʱ��']	= '~��ְʱ�䣺</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['������ѵ']	= '~>������ѵ</td>(.*?)</table>is~';
		$regex[0]['��������']	= '~��������</td>(.*?)������Ϣ</td>is~';
		$regex[0]['��������']	= '~�������ۣ�</td>\s+<td class="jl">(.*?)</td>~';
		$regex[0]['�ֻ�����']	= '~�ֻ����룺</td>\s+<td width="240" class="jl">\s+<img src="/AspNet/StrToImg\.ashx\?type=code&email=(.*?)"~';
		//$regex[0]['��ϵ�绰']	=
		$regex[0]['�����ʼ�']	= '~�����ʼ���</td>\s+<td width="240" class="jl">\s+<img src="/AspNet/StrToImg\.ashx\?type=email&email=(.*?)"~';
		$regex[0]['QQ']			= '~tencent://message/\?uin=(\d+)~';
		//$regex[0]['������վ']	=
		$regex[0]['��������']	= '~�������룺</td>\s+<td class="jl">(\d+)</td>~';
		$regex[0]['ͨѶ��ַ']	= '~ͨѶ��ַ��</td>\s+<td colspan="3" class="jl">(.*?)</td>~';

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

	public function ְλ����_0($s)
	{
		return preg_replace('/\s/','',$s);
	}//ְλ����_0

}
