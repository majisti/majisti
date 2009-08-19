package com.majisti.browser;

/**
 * Contains default or general filters. An application can then extends this interface or a class ca
 * implements it so obtain all the default and general filters of this Interface usable with the Browser.
 * 
 * @author Steven Rosato
 * @version 1.0
 */
public interface BrowserFilters
{
	/* Images */
	final static String[] commonImages = new String[]{"jpeg", "jpg", "gif", "tiff", "tif", "png", "bmp"};
	
	/* Executables */
	final static String[] commonExecutables = new String[]{"exe", "bat", "jar" };
//	final static String[] lotOfExecutables = new String[]{"exe", "bat", "jar" };

// add these for lots of executables
//	ADP - Microsoft Access Project
//	BAS - Visual Basic Class Module
//	BAT - Batch File
//	CHM - Compiled HTML Help File
//	CMD - Windows NT Command Script
//	COM - MS-DOS Application
//	CPL - Control Panel Extension
//	CRT - Security Certificate
//	DLL - Dynamic Link Library
//	DO* - Word Documents and Templates
//	EXE - Application
//	HLP - Windows Help File
//	HTA - HTML Applications
//	INF - Setup Information File
//	INS - Internet Communication Settings
//	ISP - Internet Communication Settings
//	JS - JScript File
//	JSE - JScript Encoded Script File
//	LNK - Shortcut
//	MDB - Microsoft Access Application
//	MDE - Microsoft Access MDE Database
//	MSC - Microsoft Common Console Document
//	MSI - Windows Installer Package
//	MSP - Windows Installer Patch
//	MST - Visual Test Source File
//	OCX - ActiveX Objects
//	PCD - Photo CD Image
//	PIF - Shortcut to MS-DOS Program
//	POT - PowerPoint Templates
//	PPT - PowerPoint Files
//	REG - Registration Entries
//	SCR - Screen Saver
//	SCT - Windows Script Component
//	SHB - Document Shortcut File
//	SHS - Shell Scrap Object
//	SYS - System Config/Driver
//	URL - Internet Shortcut (Uniform Resource Locator)
//	VB - VBScript File
//	VBE - VBScript Encoded Script File
//	VBS - VBScript Script File
//	WSC - Windows Script Component
//	WSF - Windows Script File
//	WSH - Windows Scripting Host Settings File
//	XL* - Excel Files and Templates 
}
