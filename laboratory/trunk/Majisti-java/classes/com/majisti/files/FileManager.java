package com.majisti.files;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;

/**
 * Classe qui contient certaines m�thodes utilitaires pour les fichiers. --
 * Traduction brought by Steven Rosato-Morin was originally written in french --
 * Many other methods were brought by Steven 
 * 
 * 
 * @author Steven Rosato-Morin & Sotira Kyprianou
 * @version 1.1
 */
public class FileManager
{

	/**
	 * La m�thode publique fermerFicSerLecture() permet de fermer un fichier
	 * s�rialis� en mode lecture.
	 * 
	 * @param nomLogique Le nom logique du fichier.
	 * @param nomFic Le nom physique du fichier.
	 * @return true si capable de fermer le fichier et false dans le cas
	 *         contraire.
	 */

	public static boolean closeFileSerRead(ObjectInputStream nomLogique, String nomFic)
	{

		boolean fermerFic = true;

		try {
			nomLogique.close();
		}

		catch ( IOException erEx ) {
			System.out.println("Error, impossible to close file " + nomFic + ".");
			fermerFic = false;
		}

		return fermerFic;
	}

	/**
	 * La m�thode publique fermerFicSerEcriture() permet de fermer un fichier
	 * s�rialis� en mode �criture.
	 * 
	 * @param nomLogique Le nom logique du fichier.
	 * @param nomFic Le nom physique du fichier.
	 * @return true si capable de fermer le fichier et false dans le cas
	 *         contraire.
	 */

	public static boolean closeFileSerWrite(ObjectOutputStream nomLogique, String nomFic)
	{

		boolean fermerFic = true;

		try {
			nomLogique.close();
		}

		catch ( IOException erEx ) {
			System.out.println("Error, impossible to close file " + nomFic + ".");
			fermerFic = false;
		}

		return fermerFic;
	}

	public static ObjectInputStream openFileSerRead(File file)
	{
		ObjectInputStream ficEntree;

		if ( !file.exists() ) {
			System.out.println("Warning: File " + file.getAbsolutePath() + " doesn't exist.");
			ficEntree = null;
		} else {
			try {
				ficEntree = new ObjectInputStream(new FileInputStream(file));
			}

			catch ( IOException erEx ) {
				System.out.println("Error, impossible to open file " + file.getAbsolutePath() + " in read mode.");
				ficEntree = null;
			}
		}

		return ficEntree;
	}

	/**
	 * La m�thode publique ouvrirFicSerLecture() permet d'ouvrir un fichier
	 * s�rialis� en mode lecture.
	 * 
	 * @param nomFichier Le nom physique du fichier.
	 * @return Le nom logique du fichier si capable de l'ouvrir et null dans le
	 *         cas contraire.
	 */

	public static ObjectInputStream openFileSerRead(String nomFichier)
	{
		File fic = new File(nomFichier);
		ObjectInputStream ficEntree;

		if ( !fic.exists() ) {
			System.out.println("Warning: File " + fic.getAbsolutePath() + " doesn't exist.");
			ficEntree = null;
		} else {
			try {
				ficEntree = new ObjectInputStream(new FileInputStream(fic));
			}

			catch ( IOException erEx ) {
				System.out.println("Error, impossible to open file " + fic.getAbsolutePath() + " in read mode.");
				ficEntree = null;
			}
		}

		return ficEntree;
	}

	public static ObjectOutputStream openFileSerWrite(File file)
	{
		ObjectOutputStream ficSortie = null;

		try {
			ficSortie = new ObjectOutputStream(new FileOutputStream(file));
		}

		catch ( IOException erEx ) {
			System.out.println("\nError, impossible to open file " + file.getAbsolutePath() + " in write mode.");
			ficSortie = null;
		}

		return ficSortie;
	}

	/**
	 * La m�thode publique ouvrirFicSerEcriture() permet d'ouvrir un fichier
	 * s�rialis� en mode �criture.
	 * 
	 * @param nomFichier Le nom physique du fichier.
	 * @return Le nom logique du fichier si capable de l'ouvrir et null dans le
	 *         cas contraire.
	 */

	public static ObjectOutputStream openFileSerWrite(String nomFichier)
	{
		File fic = new File(nomFichier);
		ObjectOutputStream ficSortie = null;

		try {
			ficSortie = new ObjectOutputStream(new FileOutputStream(nomFichier));
		}

		catch ( IOException erEx ) {
			System.out.println("\nError, impossible to open file " + fic.getAbsolutePath() + " in write mode.");
			ficSortie = null;
		}

		return ficSortie;
	}

	public static Object readObjectFromFile(File file)
	{
		ObjectInputStream inF = openFileSerRead(file);
		Object o = null;
		if ( inF != null ) {
			try {
				o = inF.readObject();
			} catch ( Exception e ) {
				o = null;
			}
			closeFileSerRead(inF, file.getName());
		}
		return o;
	}

	public static Object readObjectFromFile(String fileName)
	{
		ObjectInputStream inF = openFileSerRead(fileName);
		Object o = null;
		if ( inF != null ) {
			try {
				o = inF.readObject();
			} catch ( Exception e ) {
				o = null;
			}
			closeFileSerRead(inF, fileName);
		}
		return o;
	}

	public static String removePathLastExtension(String string)
	{
		String newString = "";
		String[] splitted = string.split("\\.");

		if ( splitted.length <= 1 ) {
			newString = string;
		} else {
			for ( int i = 0; i < splitted.length - 1; i++ ) {
				newString += splitted[i];
				if ( i < splitted.length - 2 ) {
					newString += ".";
				}
			}
		}
		return newString;
	}

	/**
	 * Attempt to check if the file given already has the extension provided, if
	 * so it returns the file. The other case, which the extension is not found
	 * at the end of the file's name, it will append it to verify if the file
	 * exists or not and return it.
	 * 
	 * @param file The file to verify on, extension at the end or not
	 * @param extension The extension to check, without the dot
	 * @return If the file exists, the file with the maybe appended extension,
	 *         null if it doesn't exist
	 */
	public static File validateFileExtension(File file, String extension)
	{
		if ( file != null ) {
			String name = file.getName();

			if ( !name.matches(".*\\." + extension + "$") ) {
				file = (new File(file.getAbsolutePath() + "." + extension));
			} else if( ! file.exists() ) {
				file = null;
			}
		}

		return file;
	}

	public static void writeObjectToFile(File file, Object o)
	{
		ObjectOutputStream outF = openFileSerWrite(file);
		if ( outF != null ) {
			try {
				outF.writeObject(o);
			} catch ( IOException ignore ) {
			}
			closeFileSerWrite(outF, file.getName());
		}
	}

	public static void writeObjectToFile(String name, Object o)
	{
		ObjectOutputStream outF = openFileSerWrite(name);
		if ( outF != null ) {
			try {
				outF.writeObject(o);
			} catch ( IOException ignore ) {
			}
			closeFileSerWrite(outF, name);
		}
	}
}