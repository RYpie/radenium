function hithere(){
    alert('hi there');
}

function addCss(){
    /*
    var mylinks = document.getElementsByTagName('link');
    foreach mylinks as l:
        if !inourplace in l.href:
            add the thing...
            */
    
    var cssId = 'inourplace_1_0_0_css';
    
    //if (!document.getElementById(cssId)) 
    {
        var head = document.getElementsByTagName('head')[0];
        var link=document.createElement('link');
        link.id=cssId;
        link.rel='stylesheet';
        link.type='text/css';
        link.href='media/com_isearch/css/inourplace.1.0.0.css';
        link.media='all';
        head.appendChild(link);
    }
}



// VIDEO PLAYER CONTAINER
// Controlling the video player bar that sticks to the top side of the browser when scrolling down.

function hideVideoPlayBar() {
    container = document.getElementById("videoplay_bar");
    container.style.display="none";
    //container.style.color="#f00";
    player=document.getElementById("videoplayercontainer");
    player.innerHTML='';
    document.getElementById("nav_height_keeper").style.height = '0px';
    
}

function playVideo(url, width, height, title, desc) {
    if (title == undefined) {
        title = "";
    }
    if (desc == undefined) {
        desc = "";
    }
    container = document.getElementById("videoplay_bar");
    container.style.display="initial";
    player=document.getElementById("videoplayercontainer");
    //player.innerHTML = "<iframe frameborder=\"0\" allowfullscreen=\"\" width=\""+width+"\" height=\""+height+"\"src=\""+url+"\" />";
    player.innerHTML = "<video controls autoplay=\"1\" width=\""+width+"\" height=\""+height+"\">" +
    		"<source src=\""+url+"\" />" +
    		"</video>";
    

    
    document.getElementById("nav_height_keeper").style.height =  getHeightKeeperHeight();
    document.getElementById("videoplayercontainer_content").innerHTML = "<h3>"+title+"</h3><p>"+desc+"</p>";

}

function getHeightKeeperHeight() {
    var bar = document.getElementById('nav');

    return bar.offsetHeight+'px';
    
}

//https://stackoverflow.com/questions/1216114/how-can-i-make-a-div-stick-to-the-top-of-the-screen-once-its-been-scrolled-to#2153775
var startProductBarPos=-1;
    window.onscroll=function(){
      var bar = document.getElementById('nav');
      if ( startProductBarPos < 0 ) {
        startProductBarPos=findPosY(bar);
      }

      
  if( pageYOffset > startProductBarPos ) {

    bar.style.position='fixed';
    bar.style.top=0;
    bar.style.paddingLeft='95px';
    document.getElementById("nav_height_keeper").style.height = bar.offsetHeight+'px';

    var vid_vis=document.getElementById("videoplay_bar");
    var vid_vis_style = window.getComputedStyle(vid_vis);
    
    //alert(vid_vis_style.getPropertyValue('display'));
    if ( vid_vis_style.getPropertyValue('display') != 'none' ) {
        document.getElementById("iop_logo_icon").style.position = 'fixed';
        document.getElementById("iop_logo_icon").style.top = '10px';
        document.getElementById("iop_logo_icon").style.left = '45px';
    }
    
  } else {
    bar.style.position='relative';
    bar.style.paddingLeft='0px';
    document.getElementById("nav_height_keeper").style.height = '0px';
    document.getElementById("iop_logo_icon").style = 'default';
    document.getElementById("iop_logo_icon").style.position = 'relative';
    document.getElementById("iop_logo_icon").style.top = '0';
    document.getElementById("iop_logo_icon").style.left = '0';
  }
};

function findPosY(obj) {
  var curtop = 0;
  if (typeof (obj.offsetParent) != 'undefined' && obj.offsetParent) {
    while (obj.offsetParent) {
      curtop += obj.offsetTop;
      obj = obj.offsetParent;
    }
    curtop += obj.offsetTop;
  }
  else if (obj.y) {
    curtop += obj.y;
  }

  return curtop;
}


//AUTOCOMPLETE

  $( function() {
    var availableTags = [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ];
    $( "#q" ).autocomplete({
      source: availableTags
    });
  } );
  
  function addtags() {
      var availableTags = [
      "jehonnes",
      "klaas",
      "harry"
    ];
    $( "#q" ).autocomplete({
      source: availableTags
    });

    
    
  }


//OTHER THINGS
function SaySomething( question ) {

$.ajax({
  dataType: "json",
  url: "http://localhost:8888/iop/components/com_isearch/bot_interface/bot.php",
  data: { 
    q: question,
    test_key: "abcdefgh",
    format: "json"
  },
  success: function( response ) {
        console.log( response ); // server response
        //alert(JSON.stringify(response));
        //alert(response.botsay)
        if ( !response.botsay ) {
        	document.getElementById("bot_say").innerHTML="Come on you, do a search or say something...";
        	document.getElementById("q").value="something";
        } else {
        	document.getElementById("bot_say").innerHTML=response.botsay;
        }
    }
});
}
function submitSaySomething(  ) {

//test = document.getElementById("question_input").value;
test = document.getElementById("q").value;
//SaySomething("hello there");
//sleep(150);
SaySomething(test);

}
function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function updateKeyList() {
	//do a call for keywords and update the autofill
    //e = e || window.event;
    e = window.event;
    var availableTags = [
        "jehonnes",
        "klaas",
        "harry"
      ];
      $( "#q" ).autocomplete({
        source: availableTags
      });
    
    
    //alert(e);
    if (e.keyCode == '38') {
        // up arrow
        return "up";
    }
    else if (e.keyCode == '40') {
        // down arrow
        return "down";
    }
    else if (e.keyCode == '37') {
       // left arrow
       return "left";
    }
    else if (e.keyCode == '39') {
       // right arrow
       return "right";
    }


    return e.keyCode;
}
