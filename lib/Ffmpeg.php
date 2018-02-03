<?php
/**
*ffmpeg类库，用于实现视频格式转换和截图等功能
*/
class Ffmpeg {
	/**
	*amr音频转换成mp3格式
	*@param $sourcepath amr文件路径
	*@param $destpath 转换后mp3的保存路径
	*/
	public function amr2mp3($sourcepath,$destpath) {
		$cmd = "ffmpeg -i $sourcepath $destpath";
		exec($cmd);
		if(file_exists($destpath)) {
			return true;
		}
		return FALSE;
	}
	/**
	*获取flv的截图
	*ffmpeg -ss 00:23:00 -s 400*300 -i Mononoke.Hime.mkv -frames:v 1 out1.jpg
	*@param $sourcepath string flv文件路径
	*@param $destpath string 截图后的保存路径
	*@param $size string 图片尺寸 400*300 格式
	*@param $sstime string 需要截图的flv播放时间点可以秒或者 hh:mm:ss[.xxx]的形式
	*/
	public function getVideoImage($sourcepath,$destpath,$size,$sstime = '10') {
		//$cmd = "ffmpeg -ss $sstime -s $size -i $sourcepath -frames:v 1 $destpath";
		$cmd = "ffmpeg -i $sourcepath -f image2 -ss $sstime -s $size -vframes 1 $destpath";
		exec($cmd);
		if(file_exists($destpath)) {
			return true;
		}
		return FALSE;
	}

}
?>