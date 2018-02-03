<?php  if ( ! defined('IN_EBH')) exit('No direct script access allowed'); ?>

ERROR  -  2017-06-02 15:30:35 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 15:30:35 --> 
UPDATE ebh_users SET wxopid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI' WHERE uid = NULL

ERROR  -  2017-06-02 15:59:21 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:05:40 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 16:05:40 --> 
UPDATE ebh_users SET wxopid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI' WHERE uid = NULL

ERROR  -  2017-06-02 16:13:52 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 16:13:52 --> 
UPDATE ebh_users SET wxopid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI' WHERE uid = NULL

ERROR  -  2017-06-02 16:18:09 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 16:18:09 --> 
UPDATE ebh_users SET wxopid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI' WHERE uid = NULL

ERROR  -  2017-06-02 16:18:10 --> 
SELECT uid,username,password FROM ebh_users  WHERE wxunionid='oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk'

ERROR  -  2017-06-02 16:18:10 --> 
insert into ebh_users(username,password,realname,dateline,sex,status,groupid) values ('wx8711172710','a3bd7c5f6cd0b0aed7ae398fdbc77913','林闲云',1496391490,'1',1,6)

ERROR  -  2017-06-02 16:18:10 --> 
insert into ebh_members(memberid,realname,sex) values (409646,'林闲云','1')

ERROR  -  2017-06-02 16:18:10 --> 
select u.username,u.realname,u.qqopid,u.sinaopid,u.email,u.mobile,u.wxopenid,b.* from ebh_users u 
					left join ebh_binds b on b.uid = u.uid 
				where u.uid = 409646
				

ERROR  -  2017-06-02 16:18:10 --> 
insert into ebh_binds(uid,is_wx,wx_str) values (409646,1,'{\"wx\":\"\",\"uid\":409646,\"openid\":\"o5TnfjtOuDq2JUCV4HsLFG9D0qKI\",\"unionid\":\"oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk\",\"nickname\":\"\\u6797\\u95f2\\u4e91\",\"dateline\":1496391490,\"from\":\"shaoma\"}')

ERROR  -  2017-06-02 16:18:10 --> 
UPDATE ebh_users SET wxopenid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI',wxunionid='oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' WHERE uid = 409646

ERROR  -  2017-06-02 16:18:10 --> 
insert into ebh_roomusers(crid,uid,cdateline,cnname,sex) values (10440,409646,1496391490,'林闲云','1')

ERROR  -  2017-06-02 16:18:10 --> 
UPDATE ebh_classes SET stunum=stunum+1 WHERE classid = 2229

ERROR  -  2017-06-02 16:18:10 --> 
UPDATE ebh_classrooms SET stunum=stunum+1 WHERE crid = 10440

ERROR  -  2017-06-02 16:18:10 --> 
insert into ebh_classstudents(uid,classid) values (409646,2229)

ERROR  -  2017-06-02 16:18:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:18:12 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:18:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:18:12 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:18:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:18:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:18:12 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:18:13 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:18:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:18:41 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:18:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:18:41 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:18:41 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:18:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:18:41 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:18:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:18:41 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:18:41 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:18:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:18:41 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:18:44 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:21:08 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:21:08 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:21:08 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:21:08 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:21:08 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:21:08 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:21:08 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:21:09 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:21:09 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:21:09 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:21:09 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:21:09 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:21:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:24:06 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:24:06 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:24:06 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:24:06 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:24:06 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:24:06 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:24:06 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:24:06 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 0
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 16:24:09 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:25:20 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:25:20 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:25:20 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:25:20 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:25:20 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:25:20 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:25:20 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:25:20 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 0
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 16:25:23 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:31:43 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:31:43 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:31:43 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:31:43 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:31:43 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:31:43 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:31:43 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:31:43 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:31:43 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:31:43 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:31:43 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:31:43 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:31:44 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 16:31:47 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:39:35 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:39:35 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:39:35 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:39:35 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:39:35 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:39:35 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:39:35 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409646

ERROR  -  2017-06-02 16:39:35 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:39:35 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:39:35 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:39:35 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409646

ERROR  -  2017-06-02 16:39:35 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:39:35 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 16:39:38 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:40:10 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 16:40:10 --> 
UPDATE ebh_users SET wxopid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI' WHERE uid = NULL

ERROR  -  2017-06-02 16:40:11 --> 
SELECT uid,username,password FROM ebh_users  WHERE wxunionid='oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk'

ERROR  -  2017-06-02 16:40:11 --> 
insert into ebh_users(username,password,realname,dateline,sex,status,groupid) values ('wx9904145305','fe239dc20857d3b957d45116c7dd486b','林闲云',1496392811,'1',1,6)

ERROR  -  2017-06-02 16:40:11 --> 
insert into ebh_members(memberid,realname,sex) values (409647,'林闲云','1')

ERROR  -  2017-06-02 16:40:11 --> 
select u.username,u.realname,u.qqopid,u.sinaopid,u.email,u.mobile,u.wxopenid,b.* from ebh_users u 
					left join ebh_binds b on b.uid = u.uid 
				where u.uid = 409647
				

ERROR  -  2017-06-02 16:40:11 --> 
insert into ebh_binds(uid,is_wx,wx_str) values (409647,1,'{\"wx\":\"\",\"uid\":409647,\"openid\":\"o5TnfjtOuDq2JUCV4HsLFG9D0qKI\",\"unionid\":\"oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk\",\"nickname\":\"\\u6797\\u95f2\\u4e91\",\"dateline\":1496392811,\"from\":\"shaoma\"}')

ERROR  -  2017-06-02 16:40:11 --> 
UPDATE ebh_users SET wxopenid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI',wxunionid='oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' WHERE uid = 409647

ERROR  -  2017-06-02 16:40:11 --> 
insert into ebh_roomusers(crid,uid,cdateline,cnname,sex) values (10440,409647,1496392811,'林闲云','1')

ERROR  -  2017-06-02 16:40:11 --> 
UPDATE ebh_classes SET stunum=stunum+1 WHERE classid = 2229

ERROR  -  2017-06-02 16:40:11 --> 
UPDATE ebh_classrooms SET stunum=stunum+1 WHERE crid = 10440

ERROR  -  2017-06-02 16:40:11 --> 
insert into ebh_classstudents(uid,classid) values (409647,2229)

ERROR  -  2017-06-02 16:40:44 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409647

ERROR  -  2017-06-02 16:40:44 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409647

ERROR  -  2017-06-02 16:40:44 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409647

ERROR  -  2017-06-02 16:40:44 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:40:44 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:40:44 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409647

ERROR  -  2017-06-02 16:40:44 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:40:44 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 16:40:47 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:41:57 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 16:41:57 --> 
UPDATE ebh_users SET wxopid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI' WHERE uid = NULL

ERROR  -  2017-06-02 16:41:58 --> 
SELECT uid,username,password FROM ebh_users  WHERE wxunionid='oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk'

ERROR  -  2017-06-02 16:41:58 --> 
insert into ebh_users(username,password,realname,dateline,sex,status,groupid) values ('wx8352835354','652ed3f22e5738ca70006efee0b1365f','林闲云',1496392918,'1',1,6)

ERROR  -  2017-06-02 16:41:58 --> 
insert into ebh_members(memberid,realname,sex) values (409648,'林闲云','1')

ERROR  -  2017-06-02 16:41:58 --> 
select u.username,u.realname,u.qqopid,u.sinaopid,u.email,u.mobile,u.wxopenid,b.* from ebh_users u 
					left join ebh_binds b on b.uid = u.uid 
				where u.uid = 409648
				

ERROR  -  2017-06-02 16:41:58 --> 
insert into ebh_binds(uid,is_wx,wx_str) values (409648,1,'{\"wx\":\"\",\"uid\":409648,\"openid\":\"o5TnfjtOuDq2JUCV4HsLFG9D0qKI\",\"unionid\":\"oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk\",\"nickname\":\"\\u6797\\u95f2\\u4e91\",\"dateline\":1496392918,\"from\":\"shaoma\"}')

ERROR  -  2017-06-02 16:41:58 --> 
UPDATE ebh_users SET wxopenid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI',wxunionid='oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' WHERE uid = 409648

ERROR  -  2017-06-02 16:41:58 --> 
insert into ebh_roomusers(crid,uid,cdateline,cnname,sex) values (10440,409648,1496392918,'林闲云','1')

ERROR  -  2017-06-02 16:41:58 --> 
UPDATE ebh_classes SET stunum=stunum+1 WHERE classid = 2229

ERROR  -  2017-06-02 16:41:58 --> 
UPDATE ebh_classrooms SET stunum=stunum+1 WHERE crid = 10440

ERROR  -  2017-06-02 16:41:58 --> 
insert into ebh_classstudents(uid,classid) values (409648,2229)

ERROR  -  2017-06-02 16:42:00 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409648

ERROR  -  2017-06-02 16:42:00 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409648

ERROR  -  2017-06-02 16:42:00 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409648

ERROR  -  2017-06-02 16:42:00 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:42:00 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:42:00 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409648

ERROR  -  2017-06-02 16:42:00 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:42:00 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 16:42:01 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:53:28 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 16:53:28 --> 
UPDATE ebh_users SET wxopid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI' WHERE uid = NULL

ERROR  -  2017-06-02 16:53:29 --> 
SELECT uid,username,password FROM ebh_users  WHERE wxunionid='oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk'

ERROR  -  2017-06-02 16:53:29 --> 
insert into ebh_users(username,password,realname,dateline,sex,status,groupid) values ('wx8991519771','8571eeda2a7eb1cb90e14b21a9817b1d','林闲云',1496393609,'1',1,6)

ERROR  -  2017-06-02 16:53:29 --> 
insert into ebh_members(memberid,realname,sex) values (409649,'林闲云','1')

ERROR  -  2017-06-02 16:53:29 --> 
select u.username,u.realname,u.qqopid,u.sinaopid,u.email,u.mobile,u.wxopenid,b.* from ebh_users u 
					left join ebh_binds b on b.uid = u.uid 
				where u.uid = 409649
				

ERROR  -  2017-06-02 16:53:29 --> 
insert into ebh_binds(uid,is_wx,wx_str) values (409649,1,'{\"wx\":\"\",\"uid\":409649,\"openid\":\"o5TnfjtOuDq2JUCV4HsLFG9D0qKI\",\"unionid\":\"oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk\",\"nickname\":\"\\u6797\\u95f2\\u4e91\",\"dateline\":1496393609,\"from\":\"shaoma\"}')

ERROR  -  2017-06-02 16:53:29 --> 
UPDATE ebh_users SET wxopenid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI',wxunionid='oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' WHERE uid = 409649

ERROR  -  2017-06-02 16:53:29 --> 
insert into ebh_roomusers(crid,uid,cdateline,cnname,sex) values (10440,409649,1496393609,'林闲云','1')

ERROR  -  2017-06-02 16:53:29 --> 
UPDATE ebh_classes SET stunum=stunum+1 WHERE classid = 2229

ERROR  -  2017-06-02 16:53:29 --> 
UPDATE ebh_classrooms SET stunum=stunum+1 WHERE crid = 10440

ERROR  -  2017-06-02 16:53:29 --> 
insert into ebh_classstudents(uid,classid) values (409649,2229)

ERROR  -  2017-06-02 16:53:31 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 16:53:31 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 16:53:31 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 16:53:31 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 16:53:31 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 16:53:31 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 16:53:31 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 16:53:31 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 16:53:32 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:04:53 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:04:53 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:04:53 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:04:53 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:04:53 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:04:53 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:04:53 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:04:53 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:04:56 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:05:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:05:45 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:05:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:05:45 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:05:45 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:05:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:05:45 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:05:45 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:05:48 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:06:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:06:56 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:06:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:06:56 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:06:56 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:06:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:06:56 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:06:56 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:06:59 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:07:58 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:07:58 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:07:58 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:07:58 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:07:58 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:07:58 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:07:58 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:07:58 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:07:58 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:07:58 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:07:58 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:07:59 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:08:14 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:14 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:08:14 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:14 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:08:14 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:08:14 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:14 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:08:14 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:08:14 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:08:14 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:08:14 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:08:14 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:08:28 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:28 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:08:28 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:28 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:08:28 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:08:39 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:39 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:08:39 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:40 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:08:40 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:08:40 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:40 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:08:40 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:40 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=13418 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:08:40 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:08:40 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:40 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=116003

ERROR  -  2017-06-02 17:08:40 --> 
course:

ERROR  -  2017-06-02 17:08:40 --> 
select notice from `ebh_coursewares` where cwid=116003

ERROR  -  2017-06-02 17:08:40 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=116003 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:08:40 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:08:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:56 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:08:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:56 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=13418 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:08:56 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:08:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:56 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:08:57 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:57 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=13418 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:08:57 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:08:57 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:08:57 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=116003

ERROR  -  2017-06-02 17:08:57 --> 
course:

ERROR  -  2017-06-02 17:08:57 --> 
select notice from `ebh_coursewares` where cwid=116003

ERROR  -  2017-06-02 17:08:57 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=116003 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:08:57 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:10:10 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:10:10 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:10:10 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:10:10 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=13418 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:10:10 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:10:10 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:10:10 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=116003

ERROR  -  2017-06-02 17:10:10 --> 
course:

ERROR  -  2017-06-02 17:10:10 --> 
select notice from `ebh_coursewares` where cwid=116003

ERROR  -  2017-06-02 17:10:10 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=116003 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:10:10 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:11:26 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:11:26 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:11:26 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:11:26 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=13418 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:11:26 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:11:27 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:11:27 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:11:27 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:11:27 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=13418 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:11:27 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:11:27 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:11:27 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=116003

ERROR  -  2017-06-02 17:11:27 --> 
course:

ERROR  -  2017-06-02 17:11:27 --> 
select notice from `ebh_coursewares` where cwid=116003

ERROR  -  2017-06-02 17:11:27 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=116003 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:11:27 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:12:54 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:12:54 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:12:54 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:12:54 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=13418 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:12:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=13418

ERROR  -  2017-06-02 17:12:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:12:56 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:12:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:12:56 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:12:56 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:12:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:12:56 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:12:56 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:12:56 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:12:56 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:12:56 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:12:56 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:13:11 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:13:11 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:13:11 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:13:11 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:13:11 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:13:11 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:13:11 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:13:11 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:13:11 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:13:11 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:13:11 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:13:11 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:13:37 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:13:37 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:13:37 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:13:37 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:13:37 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:13:39 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:13:39 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:13:39 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:13:39 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:13:39 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:13:39 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:13:39 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:13:39 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:13:39 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:13:39 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:13:39 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:13:39 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:14:38 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:14:38 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:14:38 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:14:38 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:14:38 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:14:38 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:14:38 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:14:38 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:14:38 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:14:38 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:14:38 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:14:38 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:14:58 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:14:58 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:14:58 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:14:58 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:14:58 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:14:58 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:14:58 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:14:58 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:14:58 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:14:58 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:14:58 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:14:58 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:14:58 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:14:58 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:14:58 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:14:58 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:14:58 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:15:40 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:15:40 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:15:40 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:15:40 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:15:40 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:15:40 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:15:40 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:15:40 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:15:40 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:15:40 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:15:40 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:15:40 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:15:59 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:15:59 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:15:59 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:15:59 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:15:59 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:15:59 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:15:59 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:15:59 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:15:59 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:16:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:16:12 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:16:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:16:12 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:16:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:16:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:16:12 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:16:12 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:16:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:16:22 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:16:22 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:16:22 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:16:22 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:16:22 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:16:22 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:16:22 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:16:22 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:16:22 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:16:22 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:16:22 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:16:22 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:16:22 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:16:22 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:42:50 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:42:50 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:42:50 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:42:50 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:42:50 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:42:51 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:42:51 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:42:51 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:42:51 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:42:51 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:42:51 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:42:51 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:42:51 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:42:51 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:42:51 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:42:51 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:42:51 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:43:23 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:43:23 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:43:23 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:43:23 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:43:23 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:45:28 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 17:45:28 --> 
UPDATE ebh_users SET wxopid='o5TnfjtOuDq2JUCV4HsLFG9D0qKI' WHERE uid = '409649'

ERROR  -  2017-06-02 17:45:28 --> 
select u.* from ebh_users u where ( wxopid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" ) OR (wxopenid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" )  OR (wxunionid  = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk")  limit 1

ERROR  -  2017-06-02 17:45:29 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:29 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:45:29 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:29 --> 
select c.crid as rid,c.crname as rname,c.summary,c.isschool,c.cface face,rc.enddate from ebh_roomusers rc join ebh_classrooms c on (rc.crid = c.crid) where rc.uid = 409649 and rc.cstatus = 1

ERROR  -  2017-06-02 17:45:30 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:30 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:45:30 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:30 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10440 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:45:30 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:30 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:30 --> 
select c.crid as rid,c.crname as rname,c.summary,c.isschool,c.cface face,rc.enddate from ebh_roomusers rc join ebh_classrooms c on (rc.crid = c.crid) where rc.uid = 409649 and rc.cstatus = 1

ERROR  -  2017-06-02 17:45:35 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:35 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:45:35 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:35 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10440 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:45:35 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10440 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10440 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
SELECT * FROM ebh_systemsettings WHERE crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select p.pid,p.itemid,p.crid,p.folderid,p.folderid as fid from ebh_userpermisions p WHERE p.uid=409649 AND p.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.dateline,i.providercrid,i.comfee,i.roomfee,i.providerfee,r.crname,r.summary,r.cface,r.domain,r.coursenum,r.examcount,r.ispublic,p.pname,t.tname,t.tid,i.isyouhui,i.iprice_yh,i.comfee_yh,i.roomfee_yh,i.providerfee_yh from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid left join ebh_pay_terms t on t.tid=p.tid WHERE i.status=0 AND p.status=1 AND i.crid=10440 ORDER BY itemid desc limit 0,200

ERROR  -  2017-06-02 17:45:36 --> 
SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f  WHERE f.folderid IN (3762,3465,3427,3431,3426,3425,3424) AND  f.folderlevel = 2 AND  f.power=0 ORDER BY f.displayorder limit 0,200

ERROR  -  2017-06-02 17:45:36 --> 
select isschool from ebh_classrooms where crid = 10440 limit 1

ERROR  -  2017-06-02 17:45:36 --> 
select f.folderid as fid,f.fprice,f.coursewarenum as num,f.foldername as name,f.img as face,f.uid,f.viewnum,f.summary,f.grade,f.district,f.speaker from ebh_folders f where f.folderid in (3431,3465,3427,3426,3425,3424,3762)

ERROR  -  2017-06-02 17:45:36 --> 
select pp.pid,pp.pname,pp.limitdate,pp.displayorder,pp.status,pi.itemid,pi.cannotpay,pi.sid,pi.folderid as fid,pi.iname,pi.isummary,pi.iprice,pi.iday,pi.imonth from ebh_pay_items pi join ebh_pay_packages pp on pi.pid = pp.pid where pi.folderid in (3431,3465,3427,3426,3425,3424,3762) and pi.crid = 10440 and pi.status=0 and pp.status=1 order by pp.displayorder asc

ERROR  -  2017-06-02 17:45:36 --> 
select up.itemid,up.folderid as fid,up.enddate from ebh_userpermisions up where up.folderid in (3431,3465,3427,3426,3425,3424,3762) AND up.uid =409649

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
SELECT tf.folderid,tf.tid,u.username,u.realname
			from ebh_teacherfolders tf 
			join ebh_users u on tf.tid = u.uid
			WHERE tf.folderid in (0,3431,3424,3425,3426,3427,3465)

ERROR  -  2017-06-02 17:45:36 --> 
select cw.cwid,rc.folderid,cw.cwurl,cw.title,rc.sid from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid=rc.cwid where rc.folderid in(0,3431,0,3424,3425,3426,0,3427,0,3465) AND cw.status=1 AND cw.ism3u8=1 limit 10000

ERROR  -  2017-06-02 17:45:36 --> 
select count(*) count,folderid from ebh_roomcourses rc join ebh_coursewares cw on(rc.cwid = cw.cwid) where rc.folderid in(0,3431,0,3424,3425,3426,0,3427,0,3465) AND cw.status=1 AND cw.ism3u8=1 group by rc.folderid

ERROR  -  2017-06-02 17:45:36 --> 
select cwid,ltime/ctime percent from ebh_playlogs where  cwid in(115023,115027,116729,116886,116887,115107,116762,116763,116780,116834,116854,116855,116862,115104,116754,116761,116765,116813,116814,116815) AND uid=409649 AND  totalflag=1

ERROR  -  2017-06-02 17:45:36 --> 
select cwid,sum(ltime) sumtime from ebh_playlogs  where  cwid in(115023,115027,116729,116886,116887,115107,116762,116763,116780,116834,116854,116855,116862,115104,116754,116761,116765,116813,116814,116815) AND uid=409649 AND  totalflag=0 group by cwid

ERROR  -  2017-06-02 17:45:36 --> 
select f.folderid,sum(a.totalscore/e.score) examcredit
		from ebh_schexamanswers a
		join ebh_schexams e on a.eid=e.eid
		join ebh_folders f on f.folderid = e.folderid
		 where a.uid=409649 AND f.folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465) group by f.folderid

ERROR  -  2017-06-02 17:45:36 --> 
select count(*) count,folderid from ebh_schexams e where  e.folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465) group by e.folderid

ERROR  -  2017-06-02 17:45:36 --> 
select folderid, credit, creditrule, creditmode, credittime from ebh_folders where folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465)

ERROR  -  2017-06-02 17:45:36 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.dateline,i.providercrid,i.comfee,i.roomfee,i.providerfee,r.crname,r.summary,r.cface,r.domain,r.coursenum,r.examcount,r.ispublic,p.pname,t.tname,t.tid,i.isyouhui,i.iprice_yh,i.comfee_yh,i.roomfee_yh,i.providerfee_yh from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid left join ebh_pay_terms t on t.tid=p.tid WHERE i.status=0 AND p.status=1 AND i.crid=10440 ORDER BY itemid desc limit 0,200

ERROR  -  2017-06-02 17:45:36 --> 
SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f  WHERE f.folderid IN (3762,3465,3427,3431,3426,3425,3424) AND  f.folderlevel = 2 AND  f.power=0 ORDER BY f.displayorder limit 0,200

ERROR  -  2017-06-02 17:45:36 --> 
select isschool from ebh_classrooms where crid = 10440 limit 1

ERROR  -  2017-06-02 17:45:36 --> 
select f.folderid as fid,f.fprice,f.coursewarenum as num,f.foldername as name,f.img as face,f.uid,f.viewnum,f.summary,f.grade,f.district,f.speaker from ebh_folders f where f.folderid in (3431,3465,3427,3426,3425,3424,3762)

ERROR  -  2017-06-02 17:45:36 --> 
select pp.pid,pp.pname,pp.limitdate,pp.displayorder,pp.status,pi.itemid,pi.cannotpay,pi.sid,pi.folderid as fid,pi.iname,pi.isummary,pi.iprice,pi.iday,pi.imonth from ebh_pay_items pi join ebh_pay_packages pp on pi.pid = pp.pid where pi.folderid in (3431,3465,3427,3426,3425,3424,3762) and pi.crid = 10440 and pi.status=0 and pp.status=1 order by pp.displayorder asc

ERROR  -  2017-06-02 17:45:36 --> 
select up.itemid,up.folderid as fid,up.enddate from ebh_userpermisions up where up.folderid in (3431,3465,3427,3426,3425,3424,3762) AND up.uid =409649

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
SELECT tf.folderid,tf.tid,u.username,u.realname
			from ebh_teacherfolders tf 
			join ebh_users u on tf.tid = u.uid
			WHERE tf.folderid in (0,3431,3424,3425,3426,3427,3465)

ERROR  -  2017-06-02 17:45:36 --> 
select cw.cwid,rc.folderid,cw.cwurl,cw.title,rc.sid from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid=rc.cwid where rc.folderid in(0,3431,0,3424,3425,3426,0,3427,0,3465) AND cw.status=1 AND cw.ism3u8=1 limit 10000

ERROR  -  2017-06-02 17:45:36 --> 
select count(*) count,folderid from ebh_roomcourses rc join ebh_coursewares cw on(rc.cwid = cw.cwid) where rc.folderid in(0,3431,0,3424,3425,3426,0,3427,0,3465) AND cw.status=1 AND cw.ism3u8=1 group by rc.folderid

ERROR  -  2017-06-02 17:45:36 --> 
select cwid,ltime/ctime percent from ebh_playlogs where  cwid in(115023,115027,116729,116886,116887,115107,116762,116763,116780,116834,116854,116855,116862,115104,116754,116761,116765,116813,116814,116815) AND uid=409649 AND  totalflag=1

ERROR  -  2017-06-02 17:45:36 --> 
select cwid,sum(ltime) sumtime from ebh_playlogs  where  cwid in(115023,115027,116729,116886,116887,115107,116762,116763,116780,116834,116854,116855,116862,115104,116754,116761,116765,116813,116814,116815) AND uid=409649 AND  totalflag=0 group by cwid

ERROR  -  2017-06-02 17:45:36 --> 
select f.folderid,sum(a.totalscore/e.score) examcredit
		from ebh_schexamanswers a
		join ebh_schexams e on a.eid=e.eid
		join ebh_folders f on f.folderid = e.folderid
		 where a.uid=409649 AND f.folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465) group by f.folderid

ERROR  -  2017-06-02 17:45:36 --> 
select count(*) count,folderid from ebh_schexams e where  e.folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465) group by e.folderid

ERROR  -  2017-06-02 17:45:36 --> 
select folderid, credit, creditrule, creditmode, credittime from ebh_folders where folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465)

ERROR  -  2017-06-02 17:45:36 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:36 --> 
select ru.cstatus,ru.rbalance,ru.begindate,ru.enddate from ebh_roomusers ru where ru.crid=10440 and ru.uid=409649

ERROR  -  2017-06-02 17:45:36 --> 
SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f  WHERE  f.crid = 10440 AND  f.folderlevel = 2 AND  f.isschoolfree=1 ORDER BY f.displayorder limit 100

ERROR  -  2017-06-02 17:45:36 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:45 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:45:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:45 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10440 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:45:45 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 17:45:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:45 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:45:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:45 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:45:45 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:45:46 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:46 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:45:46 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:45:46 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:45:46 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:45:46 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:45:46 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:45:52 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:52 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:45:52 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:45:52 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:45:52 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:02 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 17:46:02 --> 
select u.* from ebh_users u where ( wxopid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" ) OR (wxopenid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" )  OR (wxunionid  = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk")  limit 1

ERROR  -  2017-06-02 17:46:04 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:04 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:46:04 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:04 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:46:04 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:04 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:04 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:46:04 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:46:04 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:46:04 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:46:04 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:04 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:46:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:12 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:46:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:12 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:46:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:12 --> 
select p.pid,p.itemid,p.crid,p.folderid,p.folderid as fid from ebh_userpermisions p WHERE p.uid=409649 AND p.crid=10439

ERROR  -  2017-06-02 17:46:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:12 --> 
select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.dateline,i.providercrid,i.comfee,i.roomfee,i.providerfee,r.crname,r.summary,r.cface,r.domain,r.coursenum,r.examcount,r.ispublic,p.pname,t.tname,t.tid,i.isyouhui,i.iprice_yh,i.comfee_yh,i.roomfee_yh,i.providerfee_yh from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid left join ebh_pay_terms t on t.tid=p.tid WHERE i.status=0 AND p.status=1 AND i.crid=10439 ORDER BY itemid desc limit 0,200

ERROR  -  2017-06-02 17:46:12 --> 
SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f  WHERE f.folderid IN (4606,4421,4420,4419,4408,4407,3323,3315,3316,3317,3079,1583,3313,3314,3769,3772,3308,3763) AND  f.folderlevel = 2 AND  f.power=0 ORDER BY f.displayorder limit 0,200

ERROR  -  2017-06-02 17:46:12 --> 
select isschool from ebh_classrooms where crid = 10439 limit 1

ERROR  -  2017-06-02 17:46:12 --> 
select f.folderid as fid,f.fprice,f.coursewarenum as num,f.foldername as name,f.img as face,f.uid,f.viewnum,f.summary,f.grade,f.district,f.speaker from ebh_folders f where f.folderid in (4606,4421,4420,4419,4408,4407,3308,3323,3317,3316,3315,3314,3313,1583,3079,3772,3769,3763)

ERROR  -  2017-06-02 17:46:12 --> 
select pp.pid,pp.pname,pp.limitdate,pp.displayorder,pp.status,pi.itemid,pi.cannotpay,pi.sid,pi.folderid as fid,pi.iname,pi.isummary,pi.iprice,pi.iday,pi.imonth from ebh_pay_items pi join ebh_pay_packages pp on pi.pid = pp.pid where pi.folderid in (4606,4421,4420,4419,4408,4407,3308,3323,3317,3316,3315,3314,3313,1583,3079,3772,3769,3763) and pi.crid = 10439 and pi.status=0 and pp.status=1 order by pp.displayorder asc

ERROR  -  2017-06-02 17:46:12 --> 
select up.itemid,up.folderid as fid,up.enddate from ebh_userpermisions up where up.folderid in (4606,4421,4420,4419,4408,4407,3308,3323,3317,3316,3315,3314,3313,1583,3079,3772,3769,3763) AND up.uid =409649

ERROR  -  2017-06-02 17:46:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:12 --> 
SELECT tf.folderid,tf.tid,u.username,u.realname
			from ebh_teacherfolders tf 
			join ebh_users u on tf.tid = u.uid
			WHERE tf.folderid in (0,4606,3763,3308,3772,3769,1583,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,3314,3313)

ERROR  -  2017-06-02 17:46:12 --> 
select cw.cwid,rc.folderid,cw.cwurl,cw.title,rc.sid from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid=rc.cwid where rc.folderid in(0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) AND cw.status=1 AND cw.ism3u8=1 limit 10000

ERROR  -  2017-06-02 17:46:12 --> 
select count(*) count,folderid from ebh_roomcourses rc join ebh_coursewares cw on(rc.cwid = cw.cwid) where rc.folderid in(0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) AND cw.status=1 AND cw.ism3u8=1 group by rc.folderid

ERROR  -  2017-06-02 17:46:12 --> 
select cwid,ltime/ctime percent from ebh_playlogs where  cwid in(114539,114562,114563,114564,116865,10680,114526,10679,116867,116962,116961,10678,116868,116960,116966,116967,117001,118134,118276,118278,118279,118280,116744,116812,116859,118331,118332,118333,118334) AND uid=409649 AND  totalflag=1

ERROR  -  2017-06-02 17:46:12 --> 
select cwid,sum(ltime) sumtime from ebh_playlogs  where  cwid in(114539,114562,114563,114564,116865,10680,114526,10679,116867,116962,116961,10678,116868,116960,116966,116967,117001,118134,118276,118278,118279,118280,116744,116812,116859,118331,118332,118333,118334) AND uid=409649 AND  totalflag=0 group by cwid

ERROR  -  2017-06-02 17:46:12 --> 
select f.folderid,sum(a.totalscore/e.score) examcredit
		from ebh_schexamanswers a
		join ebh_schexams e on a.eid=e.eid
		join ebh_folders f on f.folderid = e.folderid
		 where a.uid=409649 AND f.folderid in (0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) group by f.folderid

ERROR  -  2017-06-02 17:46:12 --> 
select count(*) count,folderid from ebh_schexams e where  e.folderid in (0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) group by e.folderid

ERROR  -  2017-06-02 17:46:12 --> 
select folderid, credit, creditrule, creditmode, credittime from ebh_folders where folderid in (0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313)

ERROR  -  2017-06-02 17:46:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:12 --> 
select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.dateline,i.providercrid,i.comfee,i.roomfee,i.providerfee,r.crname,r.summary,r.cface,r.domain,r.coursenum,r.examcount,r.ispublic,p.pname,t.tname,t.tid,i.isyouhui,i.iprice_yh,i.comfee_yh,i.roomfee_yh,i.providerfee_yh from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid left join ebh_pay_terms t on t.tid=p.tid WHERE i.status=0 AND p.status=1 AND i.crid=10439 ORDER BY itemid desc limit 0,200

ERROR  -  2017-06-02 17:46:12 --> 
SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f  WHERE f.folderid IN (4606,4421,4420,4419,4408,4407,3323,3315,3316,3317,3079,1583,3313,3314,3769,3772,3308,3763) AND  f.folderlevel = 2 AND  f.power=0 ORDER BY f.displayorder limit 0,200

ERROR  -  2017-06-02 17:46:12 --> 
select isschool from ebh_classrooms where crid = 10439 limit 1

ERROR  -  2017-06-02 17:46:12 --> 
select f.folderid as fid,f.fprice,f.coursewarenum as num,f.foldername as name,f.img as face,f.uid,f.viewnum,f.summary,f.grade,f.district,f.speaker from ebh_folders f where f.folderid in (4606,4421,4420,4419,4408,4407,3308,3323,3317,3316,3315,3314,3313,1583,3079,3772,3769,3763)

ERROR  -  2017-06-02 17:46:12 --> 
select pp.pid,pp.pname,pp.limitdate,pp.displayorder,pp.status,pi.itemid,pi.cannotpay,pi.sid,pi.folderid as fid,pi.iname,pi.isummary,pi.iprice,pi.iday,pi.imonth from ebh_pay_items pi join ebh_pay_packages pp on pi.pid = pp.pid where pi.folderid in (4606,4421,4420,4419,4408,4407,3308,3323,3317,3316,3315,3314,3313,1583,3079,3772,3769,3763) and pi.crid = 10439 and pi.status=0 and pp.status=1 order by pp.displayorder asc

ERROR  -  2017-06-02 17:46:12 --> 
select up.itemid,up.folderid as fid,up.enddate from ebh_userpermisions up where up.folderid in (4606,4421,4420,4419,4408,4407,3308,3323,3317,3316,3315,3314,3313,1583,3079,3772,3769,3763) AND up.uid =409649

ERROR  -  2017-06-02 17:46:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:12 --> 
SELECT tf.folderid,tf.tid,u.username,u.realname
			from ebh_teacherfolders tf 
			join ebh_users u on tf.tid = u.uid
			WHERE tf.folderid in (0,4606,3763,3308,3772,3769,1583,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,3314,3313)

ERROR  -  2017-06-02 17:46:12 --> 
select cw.cwid,rc.folderid,cw.cwurl,cw.title,rc.sid from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid=rc.cwid where rc.folderid in(0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) AND cw.status=1 AND cw.ism3u8=1 limit 10000

ERROR  -  2017-06-02 17:46:12 --> 
select count(*) count,folderid from ebh_roomcourses rc join ebh_coursewares cw on(rc.cwid = cw.cwid) where rc.folderid in(0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) AND cw.status=1 AND cw.ism3u8=1 group by rc.folderid

ERROR  -  2017-06-02 17:46:12 --> 
select cwid,ltime/ctime percent from ebh_playlogs where  cwid in(114539,114562,114563,114564,116865,10680,114526,10679,116867,116962,116961,10678,116868,116960,116966,116967,117001,118134,118276,118278,118279,118280,116744,116812,116859,118331,118332,118333,118334) AND uid=409649 AND  totalflag=1

ERROR  -  2017-06-02 17:46:12 --> 
select cwid,sum(ltime) sumtime from ebh_playlogs  where  cwid in(114539,114562,114563,114564,116865,10680,114526,10679,116867,116962,116961,10678,116868,116960,116966,116967,117001,118134,118276,118278,118279,118280,116744,116812,116859,118331,118332,118333,118334) AND uid=409649 AND  totalflag=0 group by cwid

ERROR  -  2017-06-02 17:46:12 --> 
select f.folderid,sum(a.totalscore/e.score) examcredit
		from ebh_schexamanswers a
		join ebh_schexams e on a.eid=e.eid
		join ebh_folders f on f.folderid = e.folderid
		 where a.uid=409649 AND f.folderid in (0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) group by f.folderid

ERROR  -  2017-06-02 17:46:12 --> 
select count(*) count,folderid from ebh_schexams e where  e.folderid in (0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) group by e.folderid

ERROR  -  2017-06-02 17:46:12 --> 
select folderid, credit, creditrule, creditmode, credittime from ebh_folders where folderid in (0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313)

ERROR  -  2017-06-02 17:46:12 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:12 --> 
select ru.cstatus,ru.rbalance,ru.begindate,ru.enddate from ebh_roomusers ru where ru.crid=10439 and ru.uid=409649

ERROR  -  2017-06-02 17:46:12 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:15 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:15 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:46:15 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:15 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:46:15 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:15 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:15 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:15 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:15 --> 
select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.dateline,i.providercrid,i.comfee,i.roomfee,i.providerfee,r.crname,r.summary,r.cface,r.domain,r.coursenum,r.examcount,r.ispublic,p.pname,t.tname,t.tid,i.isyouhui,i.iprice_yh,i.comfee_yh,i.roomfee_yh,i.providerfee_yh from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid left join ebh_pay_terms t on t.tid=p.tid WHERE i.status=0 AND p.status=1 AND i.crid=10439 ORDER BY itemid desc limit 0,200

ERROR  -  2017-06-02 17:46:15 --> 
SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f  WHERE f.folderid IN (4606,4421,4420,4419,4408,4407,3323,3315,3316,3317,3079,1583,3313,3314,3769,3772,3308,3763) AND  f.folderlevel = 2 AND  f.power=0 ORDER BY f.displayorder limit 0,200

ERROR  -  2017-06-02 17:46:15 --> 
select isschool from ebh_classrooms where crid = 10439 limit 1

ERROR  -  2017-06-02 17:46:15 --> 
select f.folderid as fid,f.fprice,f.coursewarenum as num,f.foldername as name,f.img as face,f.uid,f.viewnum,f.summary,f.grade,f.district,f.speaker from ebh_folders f where f.folderid in (4606,4421,4420,4419,4408,4407,3308,3323,3317,3316,3315,3314,3313,1583,3079,3772,3769,3763)

ERROR  -  2017-06-02 17:46:15 --> 
select pp.pid,pp.pname,pp.limitdate,pp.displayorder,pp.status,pi.itemid,pi.cannotpay,pi.sid,pi.folderid as fid,pi.iname,pi.isummary,pi.iprice,pi.iday,pi.imonth from ebh_pay_items pi join ebh_pay_packages pp on pi.pid = pp.pid where pi.folderid in (4606,4421,4420,4419,4408,4407,3308,3323,3317,3316,3315,3314,3313,1583,3079,3772,3769,3763) and pi.crid = 10439 and pi.status=0 and pp.status=1 order by pp.displayorder asc

ERROR  -  2017-06-02 17:46:15 --> 
select up.itemid,up.folderid as fid,up.enddate from ebh_userpermisions up where up.folderid in (4606,4421,4420,4419,4408,4407,3308,3323,3317,3316,3315,3314,3313,1583,3079,3772,3769,3763) AND up.uid =409649

ERROR  -  2017-06-02 17:46:15 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:46:15 --> 
SELECT tf.folderid,tf.tid,u.username,u.realname
			from ebh_teacherfolders tf 
			join ebh_users u on tf.tid = u.uid
			WHERE tf.folderid in (0,4606,3763,3308,3772,3769,1583,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,3314,3313)

ERROR  -  2017-06-02 17:46:15 --> 
select cw.cwid,rc.folderid,cw.cwurl,cw.title,rc.sid from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid=rc.cwid where rc.folderid in(0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) AND cw.status=1 AND cw.ism3u8=1 limit 10000

ERROR  -  2017-06-02 17:46:15 --> 
select count(*) count,folderid from ebh_roomcourses rc join ebh_coursewares cw on(rc.cwid = cw.cwid) where rc.folderid in(0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) AND cw.status=1 AND cw.ism3u8=1 group by rc.folderid

ERROR  -  2017-06-02 17:46:15 --> 
select cwid,ltime/ctime percent from ebh_playlogs where  cwid in(114539,114562,114563,114564,116865,10680,114526,10679,116867,116962,116961,10678,116868,116960,116966,116967,117001,118134,118276,118278,118279,118280,116744,116812,116859,118331,118332,118333,118334) AND uid=409649 AND  totalflag=1

ERROR  -  2017-06-02 17:46:15 --> 
select cwid,sum(ltime) sumtime from ebh_playlogs  where  cwid in(114539,114562,114563,114564,116865,10680,114526,10679,116867,116962,116961,10678,116868,116960,116966,116967,117001,118134,118276,118278,118279,118280,116744,116812,116859,118331,118332,118333,118334) AND uid=409649 AND  totalflag=0 group by cwid

ERROR  -  2017-06-02 17:46:15 --> 
select f.folderid,sum(a.totalscore/e.score) examcredit
		from ebh_schexamanswers a
		join ebh_schexams e on a.eid=e.eid
		join ebh_folders f on f.folderid = e.folderid
		 where a.uid=409649 AND f.folderid in (0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) group by f.folderid

ERROR  -  2017-06-02 17:46:15 --> 
select count(*) count,folderid from ebh_schexams e where  e.folderid in (0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313) group by e.folderid

ERROR  -  2017-06-02 17:46:15 --> 
select folderid, credit, creditrule, creditmode, credittime from ebh_folders where folderid in (0,4606,0,3763,3308,3772,3769,0,1583,0,3079,3317,3316,3315,3323,4407,4408,4419,4420,4421,0,3314,3313)

ERROR  -  2017-06-02 17:46:22 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:22 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:46:22 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:46:22 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:46:23 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:50:44 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 17:50:44 --> 
select u.* from ebh_users u where ( wxopid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" ) OR (wxopenid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" )  OR (wxunionid  = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk")  limit 1

ERROR  -  2017-06-02 17:50:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:50:45 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:50:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:50:45 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:50:45 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:50:45 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:50:45 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:50:45 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:50:45 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:50:45 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:50:45 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:50:45 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:58:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:58:41 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:58:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:58:41 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:58:41 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:58:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:58:41 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:58:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:58:41 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:58:41 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:58:41 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:58:41 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 17:58:41 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 17:58:41 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 17:58:41 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:58:41 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:58:41 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 17:58:52 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:58:52 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 17:58:52 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 17:58:52 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 17:58:52 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 17:58:59 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 17:58:59 --> 
select u.* from ebh_users u where ( wxopid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" ) OR (wxopenid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" )  OR (wxunionid  = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk")  limit 1

ERROR  -  2017-06-02 18:03:25 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:25 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 18:03:25 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:25 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 18:03:25 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:25 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 18:03:25 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 18:03:25 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:25 --> 
select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid from ebh_coursewares c join ebh_roomcourses rc on (c.cwid = rc.cwid) join ebh_users u on (u.uid = c.uid) join ebh_folders f on (f.folderid = rc.folderid) where c.cwid=118331

ERROR  -  2017-06-02 18:03:25 --> 
course:Array
(
    [cwid] => 118331
    [uid] => 381730
    [catid] => 
    [title] => 一
    [tag] => 
    [logo] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088_178_103.jpg
    [images] => 
    [isrtmp] => 0
    [ism3u8] => 1
    [thumb] => http://img.ebanhui.com/ebh/2017/05/25/14962124309088.jpg
    [summary] => 拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意

    [message] => <p><span style="font-size: 16px;">&nbsp;拼音:yī   释义:数名，最小的正整数（在钞票和单据上常用大写“壹”代）。组词:一个，一点   成语:一心一意
</p>
    [cwname] => 1一.mp4
    [cwsource] => up.ebh.net
    [cwurl] => 2017/05/25/14962124301514.mp4
    [cwsize] => 39409499
    [dateline] => 1496212429
    [ispreview] => 0
    [status] => 1
    [submitat] => 0
    [endat] => 0
    [cwlength] => 59
    [username] => svn2svn2
    [realname] => 123
    [crid] => 10439
    [folderid] => 4606
    [sid] => 0
    [isfree] => 1
    [cdisplayorder] => 10
    [foldername] => 练习同步
    [fprice] => 1.00
    [viewnum] => 0
    [islive] => 0
    [liveid] => 
)


ERROR  -  2017-06-02 18:03:25 --> 
select notice from `ebh_coursewares` where cwid=118331

ERROR  -  2017-06-02 18:03:25 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 18:03:25 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 18:03:25 --> 
select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) where r.toid=118331 and r.type='courseware' and r.shield=0 order by r.logid desc limit 0,10

ERROR  -  2017-06-02 18:03:35 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:35 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 18:03:35 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:35 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10439 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 18:03:35 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10439

ERROR  -  2017-06-02 18:03:54 --> 
select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = 'oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk' 

ERROR  -  2017-06-02 18:03:54 --> 
select u.* from ebh_users u where ( wxopid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" ) OR (wxopenid = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk" )  OR (wxunionid  = "oD5qxs9XVWAdrcJ7TiZDAwd9T7Hk")  limit 1

ERROR  -  2017-06-02 18:03:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:56 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 18:03:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:56 --> 
select c.crid as rid,c.crname as rname,c.summary,c.isschool,c.cface face,rc.enddate from ebh_roomusers rc join ebh_classrooms c on (rc.crid = c.crid) where rc.uid = 409649 and rc.cstatus = 1

ERROR  -  2017-06-02 18:03:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:56 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 18:03:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:56 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10440 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 18:03:56 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:03:56 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:03:56 --> 
select c.crid as rid,c.crname as rname,c.summary,c.isschool,c.cface face,rc.enddate from ebh_roomusers rc join ebh_classrooms c on (rc.crid = c.crid) where rc.uid = 409649 and rc.cstatus = 1

ERROR  -  2017-06-02 18:04:53 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:53 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 18:04:53 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:53 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10440 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 18:04:53 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:53 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:53 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10440 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
select u.username,u.realname,u.nickname,u.groupid,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u join ebh_members m on (u.uid = m.memberid)  where u.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
select rm.moduleid,rm.nickname,rm.available,rm.displayorder,am.modulename,am.url,am.isdynamic,am.classname,am.target,am.isstrict,modulename_t,url_t,rm.ismore,modulecode
			from ebh_roommodules rm join ebh_appmodules am on rm.moduleid=am.moduleid where crid=10440 AND available=1 AND rm.tors in(0,2) and am.tors in (0,2) AND showmode=0 order by displayorder,moduleid limit 100

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
select p.pid,p.itemid,p.crid,p.folderid,p.folderid as fid from ebh_userpermisions p WHERE p.uid=409649 AND p.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.dateline,i.providercrid,i.comfee,i.roomfee,i.providerfee,r.crname,r.summary,r.cface,r.domain,r.coursenum,r.examcount,r.ispublic,p.pname,t.tname,t.tid,i.isyouhui,i.iprice_yh,i.comfee_yh,i.roomfee_yh,i.providerfee_yh from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid left join ebh_pay_terms t on t.tid=p.tid WHERE i.status=0 AND p.status=1 AND i.crid=10440 ORDER BY itemid desc limit 0,200

ERROR  -  2017-06-02 18:04:54 --> 
SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f  WHERE f.folderid IN (3762,3465,3427,3431,3426,3425,3424) AND  f.folderlevel = 2 AND  f.power=0 ORDER BY f.displayorder limit 0,200

ERROR  -  2017-06-02 18:04:54 --> 
select isschool from ebh_classrooms where crid = 10440 limit 1

ERROR  -  2017-06-02 18:04:54 --> 
select f.folderid as fid,f.fprice,f.coursewarenum as num,f.foldername as name,f.img as face,f.uid,f.viewnum,f.summary,f.grade,f.district,f.speaker from ebh_folders f where f.folderid in (3431,3465,3427,3426,3425,3424,3762)

ERROR  -  2017-06-02 18:04:54 --> 
select pp.pid,pp.pname,pp.limitdate,pp.displayorder,pp.status,pi.itemid,pi.cannotpay,pi.sid,pi.folderid as fid,pi.iname,pi.isummary,pi.iprice,pi.iday,pi.imonth from ebh_pay_items pi join ebh_pay_packages pp on pi.pid = pp.pid where pi.folderid in (3431,3465,3427,3426,3425,3424,3762) and pi.crid = 10440 and pi.status=0 and pp.status=1 order by pp.displayorder asc

ERROR  -  2017-06-02 18:04:54 --> 
select up.itemid,up.folderid as fid,up.enddate from ebh_userpermisions up where up.folderid in (3431,3465,3427,3426,3425,3424,3762) AND up.uid =409649

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
SELECT tf.folderid,tf.tid,u.username,u.realname
			from ebh_teacherfolders tf 
			join ebh_users u on tf.tid = u.uid
			WHERE tf.folderid in (0,3431,3424,3425,3426,3427,3465)

ERROR  -  2017-06-02 18:04:54 --> 
select cw.cwid,rc.folderid,cw.cwurl,cw.title,rc.sid from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid=rc.cwid where rc.folderid in(0,3431,0,3424,3425,3426,0,3427,0,3465) AND cw.status=1 AND cw.ism3u8=1 limit 10000

ERROR  -  2017-06-02 18:04:54 --> 
select count(*) count,folderid from ebh_roomcourses rc join ebh_coursewares cw on(rc.cwid = cw.cwid) where rc.folderid in(0,3431,0,3424,3425,3426,0,3427,0,3465) AND cw.status=1 AND cw.ism3u8=1 group by rc.folderid

ERROR  -  2017-06-02 18:04:54 --> 
select cwid,ltime/ctime percent from ebh_playlogs where  cwid in(115023,115027,116729,116886,116887,115107,116762,116763,116780,116834,116854,116855,116862,115104,116754,116761,116765,116813,116814,116815) AND uid=409649 AND  totalflag=1

ERROR  -  2017-06-02 18:04:54 --> 
select cwid,sum(ltime) sumtime from ebh_playlogs  where  cwid in(115023,115027,116729,116886,116887,115107,116762,116763,116780,116834,116854,116855,116862,115104,116754,116761,116765,116813,116814,116815) AND uid=409649 AND  totalflag=0 group by cwid

ERROR  -  2017-06-02 18:04:54 --> 
select f.folderid,sum(a.totalscore/e.score) examcredit
		from ebh_schexamanswers a
		join ebh_schexams e on a.eid=e.eid
		join ebh_folders f on f.folderid = e.folderid
		 where a.uid=409649 AND f.folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465) group by f.folderid

ERROR  -  2017-06-02 18:04:54 --> 
select count(*) count,folderid from ebh_schexams e where  e.folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465) group by e.folderid

ERROR  -  2017-06-02 18:04:54 --> 
select folderid, credit, creditrule, creditmode, credittime from ebh_folders where folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465)

ERROR  -  2017-06-02 18:04:54 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.dateline,i.providercrid,i.comfee,i.roomfee,i.providerfee,r.crname,r.summary,r.cface,r.domain,r.coursenum,r.examcount,r.ispublic,p.pname,t.tname,t.tid,i.isyouhui,i.iprice_yh,i.comfee_yh,i.roomfee_yh,i.providerfee_yh from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid left join ebh_pay_terms t on t.tid=p.tid WHERE i.status=0 AND p.status=1 AND i.crid=10440 ORDER BY itemid desc limit 0,200

ERROR  -  2017-06-02 18:04:54 --> 
SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f  WHERE f.folderid IN (3762,3465,3427,3431,3426,3425,3424) AND  f.folderlevel = 2 AND  f.power=0 ORDER BY f.displayorder limit 0,200

ERROR  -  2017-06-02 18:04:54 --> 
select isschool from ebh_classrooms where crid = 10440 limit 1

ERROR  -  2017-06-02 18:04:54 --> 
select f.folderid as fid,f.fprice,f.coursewarenum as num,f.foldername as name,f.img as face,f.uid,f.viewnum,f.summary,f.grade,f.district,f.speaker from ebh_folders f where f.folderid in (3431,3465,3427,3426,3425,3424,3762)

ERROR  -  2017-06-02 18:04:54 --> 
select pp.pid,pp.pname,pp.limitdate,pp.displayorder,pp.status,pi.itemid,pi.cannotpay,pi.sid,pi.folderid as fid,pi.iname,pi.isummary,pi.iprice,pi.iday,pi.imonth from ebh_pay_items pi join ebh_pay_packages pp on pi.pid = pp.pid where pi.folderid in (3431,3465,3427,3426,3425,3424,3762) and pi.crid = 10440 and pi.status=0 and pp.status=1 order by pp.displayorder asc

ERROR  -  2017-06-02 18:04:54 --> 
select up.itemid,up.folderid as fid,up.enddate from ebh_userpermisions up where up.folderid in (3431,3465,3427,3426,3425,3424,3762) AND up.uid =409649

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
SELECT tf.folderid,tf.tid,u.username,u.realname
			from ebh_teacherfolders tf 
			join ebh_users u on tf.tid = u.uid
			WHERE tf.folderid in (0,3431,3424,3425,3426,3427,3465)

ERROR  -  2017-06-02 18:04:54 --> 
select cw.cwid,rc.folderid,cw.cwurl,cw.title,rc.sid from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid=rc.cwid where rc.folderid in(0,3431,0,3424,3425,3426,0,3427,0,3465) AND cw.status=1 AND cw.ism3u8=1 limit 10000

ERROR  -  2017-06-02 18:04:54 --> 
select count(*) count,folderid from ebh_roomcourses rc join ebh_coursewares cw on(rc.cwid = cw.cwid) where rc.folderid in(0,3431,0,3424,3425,3426,0,3427,0,3465) AND cw.status=1 AND cw.ism3u8=1 group by rc.folderid

ERROR  -  2017-06-02 18:04:54 --> 
select cwid,ltime/ctime percent from ebh_playlogs where  cwid in(115023,115027,116729,116886,116887,115107,116762,116763,116780,116834,116854,116855,116862,115104,116754,116761,116765,116813,116814,116815) AND uid=409649 AND  totalflag=1

ERROR  -  2017-06-02 18:04:54 --> 
select cwid,sum(ltime) sumtime from ebh_playlogs  where  cwid in(115023,115027,116729,116886,116887,115107,116762,116763,116780,116834,116854,116855,116862,115104,116754,116761,116765,116813,116814,116815) AND uid=409649 AND  totalflag=0 group by cwid

ERROR  -  2017-06-02 18:04:54 --> 
select f.folderid,sum(a.totalscore/e.score) examcredit
		from ebh_schexamanswers a
		join ebh_schexams e on a.eid=e.eid
		join ebh_folders f on f.folderid = e.folderid
		 where a.uid=409649 AND f.folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465) group by f.folderid

ERROR  -  2017-06-02 18:04:54 --> 
select count(*) count,folderid from ebh_schexams e where  e.folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465) group by e.folderid

ERROR  -  2017-06-02 18:04:54 --> 
select folderid, credit, creditrule, creditmode, credittime from ebh_folders where folderid in (0,3431,0,3424,3425,3426,0,3427,0,3465)

ERROR  -  2017-06-02 18:04:54 --> 
select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

ERROR  -  2017-06-02 18:04:54 --> 
select ru.cstatus,ru.rbalance,ru.begindate,ru.enddate from ebh_roomusers ru where ru.crid=10440 and ru.uid=409649

ERROR  -  2017-06-02 18:04:54 --> 
SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f  WHERE  f.crid = 10440 AND  f.folderlevel = 2 AND  f.isschoolfree=1 ORDER BY f.displayorder limit 100

ERROR  -  2017-06-02 18:04:54 --> 
select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,u.username,u.uid from ebh_classrooms c join ebh_users u on u.uid = c.uid where c.crid=10440

