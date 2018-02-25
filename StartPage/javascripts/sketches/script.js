$(document).ready(function() {

	function fetchQuery() {

		$("#search").submit( function() {
			//Fetching the value from input element
			var searchQuery = document.getElementById('query').value;
			var wikiAPICall = "https://en.wikipedia.org/w/api.php?format=json&action=opensearch&search="+searchQuery;
			$.ajax({
				url: wikiAPICall,
				type: "GET",
				dataType: "jsonp",
				success: function(wikiData) {
					//Removing existing results
					$(".wow").remove();

					$(".search-corner").css("padding-top", "50px");

					//Checking weather any result found or not and showing the message
					if (wikiData[1][0] == undefined) {
						$(".query-title").text("No result found for '"+searchQuery+"' 😕");
					} else {
						$(".query-title").text("Search results for '"+searchQuery+"'");
					}

					//Inserting search results into UI
					for (i=0; i<wikiData[1].length; i++) {
						$(".r"+i).html("<div class='wow fadeInUp'><a href='"+wikiData[3][i]+"' target='_blank'><p>"
							+wikiData[1][i]+"</p></a><p>"+wikiData[2][i]+"</p></div>");
					}
				}
			})
			event.preventDefault();
		});

	}

  //Calling the function.
	fetchQuery();
  new WOW().init();

});

var words = document.getElementsByClassName('word');
var wordArray = [];
var currentWord = 0;

words[currentWord].style.opacity = 1;
for (var i = 0; i < words.length; i++) {
  splitLetters(words[i]);
}

function changeWord() {
  var cw = wordArray[currentWord];
  var nw = currentWord == words.length-1 ? wordArray[0] : wordArray[currentWord+1];
  for (var i = 0; i < cw.length; i++) {
    animateLetterOut(cw, i);
  }

  for (var i = 0; i < nw.length; i++) {
    nw[i].className = 'letter behind';
    nw[0].parentElement.style.opacity = 1;
    animateLetterIn(nw, i);
  }

  currentWord = (currentWord == wordArray.length-1) ? 0 : currentWord+1;
}

function animateLetterOut(cw, i) {
  setTimeout(function() {
		cw[i].className = 'letter out';
  }, i*80);
}

function animateLetterIn(nw, i) {
  setTimeout(function() {
		nw[i].className = 'letter in';
  }, 340+(i*80));
}

function splitLetters(word) {
  var content = word.innerHTML;
  word.innerHTML = '';
  var letters = [];
  for (var i = 0; i < content.length; i++) {
    var letter = document.createElement('span');
    letter.className = 'letter';
    letter.innerHTML = content.charAt(i);
    word.appendChild(letter);
    letters.push(letter);
  }

  wordArray.push(letters);
}

changeWord();
setInterval(changeWord, 4000);
