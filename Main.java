package main;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.LinkedList;
import java.util.List;
import crawler.Crawler;

public class Main {
	
	//save the links in file
	public static void saveLinksToFile(String path, List<String> crawledLinks){
		
		String filename = "ListOfLinks.txt";
		
		//Initialize file path
		File f= new File(path+filename);
		
		try{
		FileWriter fw = new FileWriter(f);
		BufferedWriter bw = new BufferedWriter(fw);
		
		
		//convert the entire document to string and write it to the file
		for(String s:crawledLinks){
			//System.out.println(s);
			bw.write(s);
			bw.newLine();
		}
		
		bw.close();
		}catch(IOException e){
			
			//error log
			try{
			BufferedWriter bw = new BufferedWriter(new FileWriter(new File("G:/KU/My_studies/Dr. Bo/information retrieval/SmartSearch_latest/crawler/Result2/Errors.log"),true));
			bw.append(filename);
			bw.newLine();
			bw.close();
			}catch(IOException e2){
				e2.printStackTrace();
			}
		}
	}
	public static void main(String[] args){

		//create instance of the crawler
		Crawler c = new Crawler();
		
		//store all the crawled links in an arraylist
		List<String> crawledLinks = new ArrayList<String>();
		
		//this is a queue that helps BFS
		List<String> fifo = new LinkedList<String>();
		
		//name of the domain
		String domain = "nytimes.com";
		
		//name of the directory where we store the crawled documents 
		String saveDirectory="G:/KU/My_studies/Dr. Bo/information retrieval/SmartSearch_latest/crawler/Result2/";
		
		//add the domain or the first link to the queue
		fifo.add("http://www."+domain);
		
		//max number of pages to crawl
		int crawlPages = 5000;
		int count = 0;
		
		//keep crawling until count reaches max number *Extra:&& (crawledLinks.size() > 0 || count == 0)
		while ( count < crawlPages && fifo.size()!=0 ){
			
			//get the first link out of the queue
			String nextPage = fifo.remove(0);
			
			//add the link to the list of links
			crawledLinks.add(nextPage);
			System.out.println("Crawling "+nextPage+"...");
			
			//stores all the links returned by the breadth first search method in the crawler class
			List<String> links = c.breadthFirst(saveDirectory,domain,nextPage);
			//System.out.println(links);
			//call the method saveToFile with 2 parameters: the name of the directory and the link
			//save.saveToFile(saveDirectory,nextPage);
			
			//add collected links to the queue to crawl
			if ( links != null){
				fifo.addAll(links);
				count++;
				System.out.println(count);
				//System.out.println(fifo);
			}
			
			try {
				Thread.sleep(500); //have a delay instead of killing the server
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		/*System.out.println("Crawled: ");
		for ( String s: crawledLinks){
			System.out.println(s);
		}*/
		
		//saveLinksToFile(saveDirectory, crawledLinks);
		
		/*System.out.println("In queue: ");
		for ( String s: fifo){
			System.out.println(s);
			//save.saveToFile(saveDirectory, s);
			try {
				Thread.sleep(500);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}*/
		
	}
}
