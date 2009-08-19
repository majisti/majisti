package com.majisti.browser;

import javax.swing.filechooser.FileFilter;

/**
 * This class creates a {@link FileFilter} with the aid of an array of Strings whicih contains
 * the wanted extensions to filter. Default extensions are provided in {@link BrowserFilters}
 * 
 * @author Steven Rosato
 * @version 1.0
 */
public class BrowserFiltersFactory
{
	/**
	 * Creates the {@link FileFilter} with the given extensions provided. A title must be assigned as well;
	 * it is the one which will be shown in the FileChooser.
	 * 
	 * @param filterTitle The Filter's title
	 * @param extensions The extensions, see {@link BrowserFilters} for default Filters
	 * 
	 * @return FileFilter The FileFilter
	 */
	public static FileFilter createFilter(String filterTitle, String ... extensions )
	{
		return new BrowserCustomFileFilter(filterTitle, extensions);
	}
}
