package savefile;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class SaveFile {

	//save the document in the specified directory
		public void saveToFile(String path, String filename,String doc){
			
			String check = ".html";
			int start = filename.indexOf('/')+2;
		    int end = 0;
		    if(filename.contains(check)){
		    	end = filename.indexOf(check); 
		    }
		    	    
		    //System.out.println(end);
			if(end!=0)
				filename = filename.substring(start,end);
			else
				filename = filename.substring(start);
			
			filename = filename.replaceAll("[/:=&?]", "");
			File f= new File(path+filename+".htm");
			
			try{
			FileWriter fw = new FileWriter(f);
			BufferedWriter bw = new BufferedWriter(fw);
			
			//get the entire page/document using the connected link and store it in local variable doc
			//Document doc = Jsoup.connect(filename).get();
			
			//convert the entire document to string and write it to the file
			bw.write(doc.toString());
			bw.write(doc);
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
}
