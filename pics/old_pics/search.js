/* InstantGallery Live Search v1.0
   Copyright ThinkMac Software 2006
   http://www.thinkmac.co.uk/instantgallery */

function GalleryImage(link, thumbnail, keywords, caption, description, relevancy)
{
  this.link = link;
  this.thumbnail = thumbnail;
  this.keywords = keywords;
  this.caption = caption;
  this.description = description;
  this.relevancy = 0;
}

function sortByRelevancy(image1, image2)
{
  return(image2.relevancy - image1.relevancy)
}

function doSearch(searchString)
{      
  if(searchString == "") searchString = document.getElementById("searchField").value;
  searchString = searchString.toLowerCase();
  var keywords = searchString.split(" ");
  if(searchString == "") return; // still nothing? give up
  var maxRelevancy = 0;
  
  for(i = 0; i < imageArray.length; i++)
  {
    imageArray[i].relevancy = 0;
    for(k = 0; k < keywords.length; k++)
    {
      var imageTags = imageArray[i].keywords.split(",");
      for(t = 0; t < imageTags.length; t++)
      {
        if(imageTags[t] == keywords[k])
        {
          imageArray[i].relevancy++; // Increment the relevancy of this item
          if(imageArray[i].relevancy > maxRelevancy) maxRelevancy = imageArray[i].relevancy;
        }
      }
    }
  } 
  
  imageArray.sort(sortByRelevancy);
  
  var html = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"resultsTable\">";
  var column = 0;
  var resultCount = 0;
    
  for(i = 0; i < imageArray.length; i++)
  {    
    if(imageArray[i].relevancy > 0) 
    {
      resultCount++;
      html += "<tr class=\"resultsRow\">";
      html += "<td class=\"resultImage\">";
      html += "<a href=\"" + imageArray[i].link + "\">";
      html += "<img src=\"" + imageArray[i].thumbnail + "\" alt=\"" + imageArray[i].caption + "\" /></a></div>";
      html += "</td><td class=\"resultInfo\">"
        html += "<a href=\"" + imageArray[i].link + "\">" + imageArray[i].caption + "</a> (" + Math.round((imageArray[i].relevancy / maxRelevancy) * 100) + "%)";
      if(imageArray[i].description != "")
      {
        html += "<p>" + imageArray[i].description + "</p>";
      }
      html += "</td></tr>";
    }
  }
  
  if(resultCount == 0) html = "<h2>No images match your search criteria</h2>";
  
  document.getElementById("results").innerHTML = html;
}