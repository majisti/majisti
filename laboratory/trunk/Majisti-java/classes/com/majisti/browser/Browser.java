package com.majisti.browser;

import java.awt.Component;
import java.io.File;

import javax.swing.JFileChooser;
import javax.swing.filechooser.FileFilter;

/**
 * This class lets a user the option to choose a file which will serve
 * to load or to save. It is possible to give filters giving the option
 * to choose several types of file.
 * 
 * @author Steven Rosato
 * @version 1.0
 */
public final class Browser 
{
	private File currentDir = null;
	private Component parent = null;
	
	public final String DEFAULTDIR = "C:";
	
	/**
	 * Empty Constructor, doesn't associate the FileChooser to a parent Component
	 */
	public Browser()
	{
	}
	
	/**
	 * Constructs this Browser object and associates it to a parent Component.
	 * 
	 * @param parent This borwser's parent
	 */
	public Browser(Component parent) 
	{
		this.parent = parent;
	}
	
	/**
	 * Gets the user chosen file from the FileChooser. The retured file can either be used
	 * to be loaded or saved. The file returned will be null if the user cancelled the operation
	 * 
	 * @param fileSelectionMode File selection mode, use FileChooser constants
	 * @param approveButtonText The approve button's text
	 * @param filters The {@link FileFilter}s to append
	 * @return The choosen file by the user or null if the user cancelled the dialog
	 */
	public File getFile(int fileSelectionMode, String approveButtonText, FileFilter ... filters) 
	{
		File dir;
		
		if(currentDir == null) {
			dir = new File(DEFAULTDIR);
		} else {
			dir = currentDir;
		}
		
		JFileChooser fc = new JFileChooser(dir);
		fc.setFileSelectionMode(fileSelectionMode);
		
		for( FileFilter filter: filters ) {
			fc.addChoosableFileFilter(filter);
		}
		
		if(fc.showDialog(parent, approveButtonText) == JFileChooser.CANCEL_OPTION) {
			return null;
		} else {
			File file = fc.getSelectedFile();
			if(file.isDirectory() && file.exists() ) {
				currentDir = file.getParentFile();
			} else if ( file.exists() ){
				currentDir = file;
			}
			return file;
		}
	}
}