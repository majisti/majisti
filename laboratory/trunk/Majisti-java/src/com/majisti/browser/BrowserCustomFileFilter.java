package com.majisti.browser;

import java.io.File;

import javax.swing.filechooser.FileFilter;

/**
 * This class will be a {@link FileFilter} according to the
 * extensions given in parameter.
 * 
 * @author Steven Rosato
 * @version 1.0
 */
public class BrowserCustomFileFilter extends FileFilter
{
	private String title;
	String[] extensions;
	
	public BrowserCustomFileFilter(final String title, final String ... extensions)
	{
		this.title = title;
		this.extensions = extensions;
	}
	
	/**
	 * Get the extension of a file.
	 */
	protected static String getExtension(File file) 
	{
		String ext = null;
		String s = file.getName();
		int i = s.lastIndexOf('.');

		if (i > 0 && i < s.length() - 1) {
			ext = s.substring(i+1).toLowerCase();
		}
		return ext;
	}

	/*
	 * (non-Javadoc)
	 * @see javax.swing.filechooser.FileFilter#accept(java.io.File)
	 */
	@Override
	public boolean accept(File file)
	{
		if (file.isDirectory()) {
			return true;
		}

		String extension = getExtension(file);
		if (extension != null) {
			boolean found = false;
			int i=0;
			while( i < extensions.length && !found ) {
				if (extension.equals(extensions[i])) {
					found = true;
				}
				i++;
			}
			return found;
		}

		return false;
	}

	/*
	 * (non-Javadoc)
	 * @see javax.swing.filechooser.FileFilter#getDescription()
	 */
	@Override
	public String getDescription()
	{
		String image = "";
		for (int i = 0; i < extensions.length; i++) {
			image += "." + extensions[i];
			
			if(i < extensions.length - 1) {
				image += ", ";
			}
		}
		return title + " ("+image+")";
	}
}
