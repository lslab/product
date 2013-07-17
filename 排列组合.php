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
/* * 排列组合回溯算法：
* 1. 从 $source 尾部开始往前寻找两个相邻元素（$x,$y)，这两个元素的值满足条件（$x < $y），即前一个比后一个元素大；
* 2. 再次从 $source 尾部开始向前查找，找出第一个大于 $x 的元素（$z），交换 $x 和 $z 元素的值；
* 3. 将 $y 及其之后的所有元素逆向排列，所得排列即为 $source 的下一个组合排列形式。
*/
$source = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',0,1,2,3,4,5,6,7,8,9,'.','-','_');
sort($source); //保证初始数组是有序的
$last = count($source) - 1; //$source尾部元素下标
$x = $last;
$count = 1; //组合个数统计
echo implode(',', $source), "\n"; //输出第一种组合
while (true) {
    $y = $x--; //相邻的两个元素
    if ($source[$x] < $source[$y]) { //如果前一个元素的值小于后一个元素的值
        $z = $last;
        while ($source[$x] > $source[$z]) { //从尾部开始，找到第一个大于 $x 元素的值
            $z--;
        }
        /* 交换 $x 和 $z 元素的值 */
        list($source[$x], $source[$z]) = array($source[$z], $source[$x]);
        /* 将 $y 之后的元素全部逆向排列 */
        for ($i = $last; $i > $y; $i--, $y++) {
            list($source[$i], $source[$y]) = array($source[$y], $source[$i]);
        }
        echo implode(',', $source), "\n"; //输出组合
        $x = $last;
        $count++;
    }
    if ($x == 0) { //全部组合完毕
        break;
    }
}
echo 'Total: ', $count, "\n";