﻿/*
 * CKFinder
 * ========
 * http://www.ckfinder.com
 * Copyright (C) 2007-2008 Frederico Caldeira Knabben (FredCK.com)
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 *
 * ---
 * English language file.
 */

var CKFLang =
{

Dir : 'ltr',
HelpLang : 'en',

// Date Format
//		d    : Day
//		dd   : Day (padding zero)
//		m    : Month
//		mm   : Month (padding zero)
//		yy   : Year (two digits)
//		yyyy : Year (four digits)
//		h    : Hour (12 hour clock)
//		hh   : Hour (12 hour clock, padding zero)
//		H    : Hour (24 hour clock)
//		HH   : Hour (24 hour clock, padding zero)
//		M    : Minute
//		MM   : Minute (padding zero)
//		a    : Firt char of AM/PM
//		aa   : AM/PM
DateTime : 'yyyy年m月d日 h:MM aa',
DateAmPm : ['AM','PM'],

// Folders
FoldersTitle	: '文件夹',
FolderLoading	: '正在加载文件夹...',
FolderNew	: '请输入新文件夹名称: ',
FolderRename	: '请输入新文件夹名称: ',
FolderDelete	: '您确定要删除文件夹 "%1" 吗?',
FolderRenaming	: ' (正在重命名...)',
FolderDeleting	: ' (正在删除...)',

// Files
FileRename	: '请输入新文件名: ',
FileRenameExt	: '如果改变文件扩展名，可能会导致文件不可用。\r\n确定要更改吗？',
FileRenaming	: '正在重命名...',
FileDelete	: '您确定要删除文件 "%1" 吗?',

// Toolbar Buttons (some used elsewhere)
Upload		: '上传',
UploadTip	: '上传文件',
Refresh		: '刷新',
Settings	: '设置',
Help		: '帮助',
HelpTip		: '查看在线帮助',

// Context Menus
Select		: '选择',
View		: '查看',
Download	: '下载',

NewSubFolder	: '创建子文件夹',
Rename		: '重命名',
Delete		: '删除',

// Generic
OkBtn		: '确定',
CancelBtn	: '取消',
CloseBtn	: '关闭',

// Upload Panel
UploadTitle		: '上传文件',
UploadSelectLbl		: '选定要上传的文件',
UploadProgressLbl	: '(正在上传文件，请稍候...)',
UploadBtn		: '上传选定的文件',

UploadNoFileMsg		: '请选择一个要上传的文件',

// Settings Panel
SetTitle		: '设置',
SetView			: '查看:',
SetViewThumb	: '缩略图',
SetViewList		: '列表',
SetDisplay		: '显示:',
SetDisplayName	: '文件名',
SetDisplayDate	: '日期',
SetDisplaySize	: '大小',
SetSort			: '排列顺序:',
SetSortName		: '按文件名',
SetSortDate		: '按日期',
SetSortSize		: '按大小',

// Status Bar
FilesCountEmpty : '<空文件夹>',
FilesCountOne	: '1 个文件',
FilesCountMany	: '%1 个文件',

// Connector Error Messages.
ErrorUnknown : '请求的操作未能完成. (错误 %1)',
Errors : 
{
 10 : '无效的指令.',
 11 : 'The resource type was not specified in the request.',
 12 : 'The requested resource type is not valid.',
102 : '无效的文件名或文件夹名称.',
103 : 'It was not possible to complete the request due to authorization restrictions.',
104 : 'It was not possible to complete the request due to file system permission restrictions.',
105 : '无效的扩展名.',
109 : '无效请求.',
110 : '未知错误.',
115 : '存在重名的文件或文件夹.',
116 : '文件夹不存在. 请刷新后再试.',
117 : '文件不存在. 请刷新列表后再试.',
201 : '文件与现有的重名. 新上传的文件改名为 "%1"',
202 : '无效的文件',
203 : '无效的文件. 文件尺寸太大.',
204 : '上传文件已损失.',
205 : 'No temporary folder is available for upload in the server.',
206 : 'Upload cancelled for security reasons. The file contains HTML like data.',
500 : 'The file browser is disabled for security reasons. Please contact your system administrator and check the CKFinder configuration file.',
501 : 'The thumbnails support is disabled.'
},

// Other Error Messages.
ErrorMsg :
{
FileEmpty		: '文件名不能为空',
FolderEmpty		: '文件夹名称不能为空',

FileInvChar		: '文件名不能包含以下字符: \n\\ / : * ? " < > |',
FolderInvChar	: '文件夹名称不能包含以下字符: \n\\ / : * ? " < > |',

PopupBlockView	: '未能在新窗口中打开文件. 请修改浏览器配置解除对本站点的锁定.'
}

} ;
