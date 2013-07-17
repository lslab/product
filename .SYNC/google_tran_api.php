<pre>
<?php

echo $text="一封密的瓶子，瓶中装有一定量的水。然后倒过来放，水对瓶底的压力怎么变化";
echo "\n";
echo $text=tran($text,'zh-cn','en');
echo "\n";
echo $text=tran($text,'en','zh-cn');
echo "\n";
echo $text=tran($text,'zh-cn','en');
echo "\n";
echo $text=tran($text,'en','zh-cn');

function tran($str,$fromlang,$tolang) {
	$text=urlencode($str);
	$url = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=$text&hl=zh_cn&langpair=$fromlang%7C$tolang"; 
	 
	// sendRequest 
	// note how referer is set manually 
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	//curl_setopt($ch, CURLOPT_REFERER, "http://demo.xiaohd.com/test.php"); 
	$body = curl_exec($ch); 
	curl_close($ch); 
	 
	// now, process the JSON string 
	//echo $body;
	$json = json_decode($body); 
	//print_r($json);

	$text = $json->responseData->translatedText;
	return $text;
}