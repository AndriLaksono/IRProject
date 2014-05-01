%%%--- This Script reads files from a specific directory ---%%%
%%%---        Each line in a File has one string         ---%%%
%%%---   This script sorts all the strings in all files  ---%%%
%%%---             by Arunabha Choudhury                 ---%%%

% specify absolute path
abspath = 'G:\KU\My_studies\Dr. Bo\information retrieval\SmartSearch_latest\Results\unsorted\';
outputfile = 'G:\KU\My_studies\Dr. Bo\information retrieval\SmartSearch_latest\Results\sorted\';

% find all the files in the path directory
fnames = dir(strcat(abspath,'*.txt'));

% count the number of files
numfiles = length(fnames);

%gets the name of the file from the cell array
filename = fnames(1).name;
    
%reads the file and stores it in a string called text
text = fileread(strcat(abspath,filename));
    
%converts the text to a cell array
[words] = strread(text, '%s\r\n');

%sorts the cell array containing the words
[uWords,junk,indx] = unique(words);
wordFreq = histc(indx,1:numel(uWords));

newWords=strcat(uWords,',',num2str(1),',',num2str(wordFreq));

% for each file, get content and sort it and write it back to the file
for i=2:numfiles
    
    %gets the name of the file from the cell array
    filename = fnames(i).name;
    
    %reads the file and stores it in a string called text
    text = fileread(strcat(abspath,filename));
    
    %converts the text to a cell array
    [words] = strread(text, '%s\r\n');
    
    %sorts the cell array containing the words
    [uWords,junk,indx] = unique(words);
    wordFreq = histc(indx,1:numel(uWords));    
   
    %make new cell array consiting of comma seperated values
    tempWords = strcat(uWords,',',num2str(i),',',num2str(wordFreq));
    
    newWords(end+1:end+numel(tempWords))=tempWords;

end

tic
sortedWords = sort(newWords);
toc

div = 5;

%divide the file in div parts
interval = floor(numel(sortedWords)/div);
iter = 1;

for i=1:div-1
    filename = strcat('II',num2str(i),'.csv');
    data = sortedWords(iter:i*interval);
    iter = 1+i*interval;
    
    %opens the existing file in absolute path specified
    fileid = fopen(strcat(outputfile,filename),'w');
    
    %writes the new content to the old file replacing the content
    fprintf(fileid,'%s\r\n',data{1:end-1});
    fprintf(fileid,'%s',data{end});
    
    %close file
    fclose(fileid);

    clear data;
end

filename = strcat('II',num2str(div),'.csv');
data = sortedWords(iter:end);
    
%opens the existing file in absolute path specified
fileid = fopen(strcat(outputfile,filename),'w');
    
%writes the new content to the old file replacing the content
fprintf(fileid,'%s\r\n',data{1:end-1});
fprintf(fileid,'%s',data{end});
    
%close file
fclose(fileid);

% filename = 'InvertedIndex.csv';
% 
% %opens the existing file in absolute path specified
% fileid = fopen(strcat(outputfile,filename),'w');
%     
% %writes the new content to the old file replacing the content
% fprintf(fileid,'%s\r\n',sortedWords{1:end-1});
% fprintf(fileid,'%s',sortedWords{end});
%     
% %close file
% fclose(fileid);

clear all;
close all;
% exit;
