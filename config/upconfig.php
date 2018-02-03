<?php
/*
 * 文件上传相关配置文件
 */
 //课件服务器配置路径
$upconfig['course']['server'][0] = 'http://c1.ebanhui.com/upload.html';
$upconfig['course']['savepath'] = '/data0/uploads/courses/';
$upconfig['course']['showpath'] = '/uploads/courses/';
//$upconfig['course']['servers'][1] = 'http://file2.ebanhui.com/sitecp.php?action=upload';		

//图片服务器配置路径
$upconfig['pic']['server'][0] = 'http://c1.ebanhui.com/uploadimage.html';		
$upconfig['pic']['savepath'] = '/data0/htdocs/img/ebh/';
$upconfig['pic']['showpath'] = 'http://img.ebanhui.com/ebh/';

//头像服务器配置路径
$upconfig['avatar']['server'][0] = 'http://c1.ebanhui.com/uploadimage.html';		
$upconfig['avatar']['savepath'] = '/data0/htdocs/img/avatar/';
$upconfig['avatar']['showpath'] = 'http://img.ebanhui.com/avatar/';

//互动课堂服务器配置路径
$upconfig['iroom']['server'][0] = 'http://c1.ebanhui.com/uploadimage.html';		
$upconfig['iroom']['savepath'] = '/data0/htdocs/img/iroom/';
$upconfig['iroom']['showpath'] = 'http://img.ebanhui.com/iroom/';

//电子教室附件配置
$upconfig['attachment']['server'][0] = 'http://c1.ebanhui.com/upload.html';
$upconfig['attachment']['savepath'] = '/data0/uploads/docs/';
$upconfig['attachment']['showpath'] = '/uploads/docs/';

//资源文件配置
$upconfig['rfile']['server'][0] = '/sitecp.php?action=upfile';
$upconfig['rfile']['savepath'] = '/data0/uploads/rfiles/';
$upconfig['rfile']['showpath'] = '/uploads/rfiles/';

//笔记文件配置
$upconfig['note']['savepath'] = '/data0/uploads/notes/';
$upconfig['note']['showpath'] = '/uploads/notes/';
//笔记附件文件配置
$upconfig['noteatta']['savepath'] = '/data0/htdocs/file/noteatta/';
//笔记附件文件显示配置
$upconfig['noteatta']['showpath'] = 'http://file.ebanhui.com/noteatta/';

//课程附件上传配置
$upconfig['courseatta']['data']['savepath'] = '/data0/htdocs/file/cuploads/';
//课程附件上传配置
$upconfig['courseatta']['data']['showpath'] = 'http://file.ebanhui.com/cuploads/';

//上传作业配置
$upconfig['stuexam']['server'][0] = '/sitecp.php?action=upatt&type=stuexam';
$upconfig['stuexam']['savepath'] = '/data0/uploads/exam/';
$upconfig['stuexam']['path'] = '/exam/';
$upconfig['stuexam']['showpath'] = '/uploads/exam/';

//原创空间
//原创作品文件配置
$upconfig['space']['savepath'] = '/data0/uploads/space/';
$upconfig['space']['showpath'] = '/uploads/space/';
$upconfig['space']['imagepath'] = 'http://img.ebanhui.com/space/';

 //作业课件(主观题)上传位置配置
$upconfig['examcourse']['server'][0] = '/sitecp.php?action=examcourse';
$upconfig['examcourse']['savepath'] = '/data0/uploads/examcourse/';
$upconfig['examcourse']['showpath'] = '/uploads/examcourse/';

//音频服务器配置路径
$upconfig['audio']['server'][0] = '/sitecp.php?action=upimage&uptype=audio';		
$upconfig['audio']['savepath'] = '/data0/htdocs/img/audio/';  
$upconfig['audio']['showpath'] = 'http://img.ebanhui.com/audio/';

 //临时文件上传目录，如导入学生等xls的临时目录等
$upconfig['temp']['savepath'] = '/data0/uploads/temp/';
$upconfig['temp']['showpath'] = '/uploads/temp/';

//swf
$upconfig['reslibs']['savepath'] = '/data0/uploads/swf/';
$upconfig['reslibs']['showpath'] = '/uploads/swf/';

//电子教案附件配置
$upconfig['tplanatt']['savepath'] = '/data0/uploads/tplanatt/';
$upconfig['tplanatt']['showpath'] = '/uploads/tplanatt/';

//答疑相关附件(图片/音频等)服务器配置路径
$upconfig['ask']['server'][0] = 'http://c1.ebanhui.com/uploadimage.html';		
$upconfig['ask']['savepath'] = '/data0/htdocs/img/ask/';
$upconfig['ask']['showpath'] = 'http://img.ebanhui.com/ask/';

 //作业课件(主观题)相关图片路径配置
$upconfig['examcoursepic']['server'][0] = '/sitecp.php?action=examcourse';
$upconfig['examcoursepic']['savepath'] = '/data0/htdocs/img/examcourse/';
$upconfig['examcoursepic']['showpath'] = 'http://img.ebanhui.com/examcourse/';

$upconfig['formula']['savepath'] = '/data0/htdocs/img/formula/';
$upconfig['formula']['showpath'] = 'http://img.ebanhui.com/formula/';



//课件类型为doc/ppt/xls等格式时所提供的预览处理路径
$upconfig['preview']['url'] = 'http://192.168.0.11:887/index.aspx';

$upconfig['fnote']['savepath'] = '/data0/htdocs/img/fnote/';
$upconfig['fnote']['showpath'] = 'http://img.ebanhui.com/fnote/';
