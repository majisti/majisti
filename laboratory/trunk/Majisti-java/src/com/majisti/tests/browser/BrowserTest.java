package com.majisti.tests.browser;

import java.io.File;

import javax.swing.JFileChooser;

import junit.framework.TestCase;

import org.junit.Test;

import com.majisti.browser.Browser;
import com.majisti.browser.BrowserFilters;
import com.majisti.browser.BrowserFiltersFactory;


public class BrowserTest extends TestCase
{
	private Browser browser = new Browser();
	
	@Test
	public void testGetFile()
	{
		for ( int i = 0; i < 3; i++ ) {
			File file = browser.getFile(JFileChooser.FILES_AND_DIRECTORIES, 
					"Select", 
					BrowserFiltersFactory.createFilter(
							"Just Executables", 
							BrowserFilters.commonExecutables)
					);
			System.out.println("Path: " + file.getAbsolutePath());
			System.out.println("Name: " + file.getName());
		}
	}
}
