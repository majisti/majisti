package com.majisti.document;

import javax.swing.text.AttributeSet;
import javax.swing.text.BadLocationException;
import javax.swing.text.DocumentFilter;
import javax.swing.text.PlainDocument;

/**
 * Document with a specified max length.
 * The length is controlled by the MaxLengthDocumentFilter.
 * 
 * @author natha
 */
public class MaxLengthDocument extends PlainDocument 
{
	private static final long serialVersionUID = 1L;

	/**
	 * Create a Document with a specified max length.
	 * 
	 * @param maxLength The maximum length permitted in this document.
	 */
	public MaxLengthDocument(int maxLength) 
	{
		this.setDocumentFilter(new MaxLengthDocumentFilter(maxLength));
	}
 
	/**
	 * DocumentFilter that won't let a too long string in the document content.
	 * Can be used by any Document that have the setDocumentFilter() method.
	 * 
	 * @author natha
	 */
	public static class MaxLengthDocumentFilter extends DocumentFilter 
	{
		/** Max length autorized for the document */
		private int maxlength;
 
		/**
		 * Default Constructor with maxlength if not associated to a JFormattedTextField.
		 * 
		 * @param maxlength
		 */
		public MaxLengthDocumentFilter(int maxlength) 
		{
			this.maxlength = maxlength;
		}
 
		/* (non-Javadoc)
		 * @see javax.swing.text.DocumentFilter#insertString(javax.swing.text.DocumentFilter.FilterBypass, int, java.lang.String, javax.swing.text.AttributeSet)
		 */
		@Override
		public void insertString(FilterBypass fb, int offset, String text, AttributeSet attr) throws BadLocationException 
		{
			if (text != null && text.length() + fb.getDocument().getLength() > maxlength) {
				text = text.substring(0, text.length() - (text.length() + fb.getDocument().getLength() - maxlength));
			}
			super.insertString(fb, offset, text, attr);
		}
 
		/* (non-Javadoc)
		 * @see javax.swing.text.DocumentFilter#remove(javax.swing.text.DocumentFilter.FilterBypass, int, int)
		 */
		@Override
		public void remove(FilterBypass fb, int offset, int length) throws BadLocationException 
		{
			super.remove(fb, offset, length);
		}
 
		/* (non-Javadoc)
		 * @see javax.swing.text.DocumentFilter#replace(javax.swing.text.DocumentFilter.FilterBypass, int, int, java.lang.String, javax.swing.text.AttributeSet)
		 */
		@Override
		public void replace(FilterBypass fb, int offset, int length, String text, AttributeSet attrs) throws BadLocationException
		{
			if (text != null && text.length() + fb.getDocument().getLength() - length > maxlength) {
				text = text.substring(0, text.length() - (text.length() + fb.getDocument().getLength() - length - maxlength));
			}
			super.replace(fb, offset, length, text, attrs);
		}
	}
 
}
