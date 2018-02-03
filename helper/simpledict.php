<?php
/**
 * 检查是否有敏感词语
 * @param  string $searchstr 需要检查的字符串
 * @return boolean     TRUE通过 FALSE失败
 */
function checkSensitive($searchstr){
		$dict = Ebh::app()->lib('SimpleDict');

		$searchstr = strip_tags($searchstr);
		//正则表达式去除所有空格和html标签（包括换行 空格 &nbsp;）
		$searchstr = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", $searchstr);

		$result = $dict->search($searchstr);

		if (!empty($result)){
			return FALSE;
		}

		return TRUE;
}