<?php
$source = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',0,1,2,3,4,5,6,7,8,9,'.','-','_');
$source = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
$len=count($source);
$n=0;
for ($i=0; $i<$len; $i++) {
	for ($j=$i; $j<$len; $j++) {
		for ($k=$j; $k<$len; $k++) {
			for ($l=0; $l<$len; $l++) {
				$r=array();
				$r[]=$source[$i];
				$r[]=$source[$j];
				$r[]=$source[$k];
				$r[]=$source[$l];
				$s=var_export($r,true);
				echo $s;die;
				unset($r);
				$n++;
			}

		}
	}
}



////////////////////////////////////////////////////////////////
/* * ������ϻ����㷨��
* 1. �� $source β����ʼ��ǰѰ����������Ԫ�أ�$x,$y)��������Ԫ�ص�ֵ����������$x < $y������ǰһ���Ⱥ�һ��Ԫ�ش�
* 2. �ٴδ� $source β����ʼ��ǰ���ң��ҳ���һ������ $x ��Ԫ�أ�$z�������� $x �� $z Ԫ�ص�ֵ��
* 3. �� $y ����֮�������Ԫ���������У��������м�Ϊ $source ����һ�����������ʽ��
*/
$source = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',0,1,2,3,4,5,6,7,8,9,'.','-','_');
sort($source); //��֤��ʼ�����������
$last = count($source) - 1; //$sourceβ��Ԫ���±�
$x = $last;
$count = 1; //��ϸ���ͳ��
echo implode(',', $source), "\n"; //�����һ�����
while (true) {
    $y = $x--; //���ڵ�����Ԫ��
    if ($source[$x] < $source[$y]) { //���ǰһ��Ԫ�ص�ֵС�ں�һ��Ԫ�ص�ֵ
        $z = $last;
        while ($source[$x] > $source[$z]) { //��β����ʼ���ҵ���һ������ $x Ԫ�ص�ֵ
            $z--;
        }
        /* ���� $x �� $z Ԫ�ص�ֵ */
        list($source[$x], $source[$z]) = array($source[$z], $source[$x]);
        /* �� $y ֮���Ԫ��ȫ���������� */
        for ($i = $last; $i > $y; $i--, $y++) {
            list($source[$i], $source[$y]) = array($source[$y], $source[$i]);
        }
        echo implode(',', $source), "\n"; //������
        $x = $last;
        $count++;
    }
    if ($x == 0) { //ȫ��������
        break;
    }
}
echo 'Total: ', $count, "\n";