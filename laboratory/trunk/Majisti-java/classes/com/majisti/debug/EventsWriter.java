package com.majisti.debug;


/**
 * This class helps providing decorated output when developping.
 * A good application is not only user-friendly with messages for the GUI
 * but user-friendly with a console output as well for the developers.
 * 
 * @author Steven
 */
public class EventsWriter
{
	
	/**
	 * Prints a line with a \n at the end.
	 * 
	 * @param line The line to output
	 */
	public static void println(String line)
	{
		System.out.println(line);
	}
	
	/**
	 * Prints a line with a \n at the end, custom variables
	 * may be added in the line with additionnal objects passed in
	 * parameters after the line.
	 * 
	 * For more information, refer to {@link java.io.PrintStream}
	 * 
	 * @param format The line to print, with special formatting in it according to next passed objects in parameters.
	 * @param args The objects that will serve as a replacement in the formatted line.
	 */
	public static void printfln(String format, Object ... args)
	{
		System.out.printf(format, args);
		System.out.println();
	}
}
