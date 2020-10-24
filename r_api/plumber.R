#
# This is a Plumber API. You can run the API by clicking
# the 'Run API' button above.
#
# Find out more about building APIs with Plumber here:
#
#    https://www.rplumber.io/
# curl "http://localhost:9814/title?title=Hello%20my%20name%20is%20ERIC"

library(plumber)
library(tidyverse)
library(SnowballC)
library(tensorflow)
library(keras)

#* @apiTitle Plumber Example API

#* Process title and tokenize
#* @param title 
#* @get /title
function(title){
  new_title <- str_to_lower(title)
  new_title <- str_replace_all(new_title,"\\$\\S+","dollar")
  new_title <- str_replace_all(new_title,"\\d+","number")
  new_title <- str_replace_all(new_title,"[^\\w|\\s]","")
  new_title <- strsplit(new_title, " ")
  new_title <- lapply(new_title,wordStem, language="en")
  new_title <- lapply(new_title,trimws)
  new_title <- new_title[new_title != ""]
  token <- load_text_tokenizer("tokenizer")
  seq <- texts_to_sequences(token, new_title)
  inp <- pad_sequences(seq, maxlen = 44)
  return(inp)
}


