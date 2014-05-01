package crawler;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.List;

import org.jsoup.Connection;
import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import savefile.SaveFile;

public class Crawler {
	
	//this hash set instance variable stores the links that it has already seen 
	HashSet<String> seenIt;
	
	//constructor initializes the instance variable
	public Crawler(){
		seenIt = new HashSet<String>();
	}
	
	/* The method breadth first search collects all the links in the given page.
	 * It takes in the domain and the url of the page as input.
	 * The output is a list of link in that page.
	 */
	public List<String> breadthFirst(String saveDirectory,String domain, String startURL){
		
		//Define an array list that will store all the collected links from the given page
		List<String> links = null;
		
		//If page already seen, return null list
		/*if ( seenIt.contains(startURL)){
			return null;
		}*/
		
		//add start url to the list of seen pages 
		seenIt.add(startURL);
		
		//use Jsoup to connect to the url and collect links from the url
		try{
			//initialize object c of type Connection (Jsoup method connect takes url and returns Connection)
			Connection c = Jsoup.connect(startURL);
			
			//get the entire page/document using the connected link
			Document doc = c.get();
			
			//call the method saveToFile in class saevfile with 2 parameters: the name of the directory and the link
			//create instance of save file class
			SaveFile save = new SaveFile();
			save.saveToFile(saveDirectory,startURL,doc.toString());
			
			//get only Anchor tag element from the document to collect the links
			Elements as = doc.getElementsByTag("a");
			
			//Instantiate the array list 
			links = new ArrayList<String>();
			
			//for each anchor tag in Element parse the tag and only keep the link
			for ( Element a : as){
				
				//get the absolute url link from href
				String href = a.absUrl("href");
				
				//
				if ( href.contains("#")){
					href = href.substring(0,href.indexOf("#"));
				}
				
				//
				if (href.startsWith("#")|| href.startsWith("mailto")|| href.equals("")){
					continue;
				}
				
				//remove the last / to get only url
				if ( href.endsWith("/")){
					href =href.substring(0,href.length()-1);
				}
				
				//if not already seen and if belongs to the specified domain, add the link to the list
				if (!seenIt.contains(href) && href.contains(domain)){
					seenIt.add(href);
					links.add(href);
				}
			}

		}catch(IOException e){
			e.printStackTrace();
		}
		return links;
	}

}
