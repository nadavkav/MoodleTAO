window.slideAnimation_numberOfIntervals=6;
window.slideAnimation_intervalDuration=12;//milliseconds
window.comment_closedHeight=93;


function openAll(){
 for(var i=0;i<window.comment_ids.length;i++)
 slideOpen(window.comment_ids[i],i+1)
}

function slideOpen(e,comment){
 if(typeof e=="string")e=document.getElementById(e);if(!e)return;
 var c=e.getAttribute("id");if(!c)return;
 if(!window.comment_height||typeof window.comment_height[c]=="undefined")return;
 if(!window.comment_toggleFlag||typeof window.comment_toggleFlag[c]=="undefined")return;
 if(window.comment_toggleFlag[c]!="closed")return;
 window.comment_toggleFlag[c]="opening";
 var orginal_height=window.comment_height[c];
 var height_incre=(orginal_height-comment_closedHeight)/window.slideAnimation_numberOfIntervals
 var current_height=comment_closedHeight;
 var k=setInterval(function(){current_height+=height_incre; if(current_height<orginal_height)e.style.height=current_height+"px";else{e.style.height=orginal_height+"px";clearInterval(k);window.comment_toggleFlag[c]="open"};},window.slideAnimation_intervalDuration);
 document.getElementById('close_'+comment).style.display='';
 document.getElementById('view_comment_'+comment).style.display='none';
}

function slideClosed(e,comment){
 if(typeof e=="string")e=document.getElementById(e);if(!e)return;
 var c=e.getAttribute("id");if(!c)return;
 if(!window.comment_height||typeof window.comment_height[c]=="undefined")return;
 if(!window.comment_toggleFlag||typeof window.comment_toggleFlag[c]=="undefined")return;
 if(window.comment_toggleFlag[c]!="open")return;
 window.comment_toggleFlag[c]="closing";
 var orginal_height=window.comment_height[c];
 var height_incre=(orginal_height-comment_closedHeight)/window.slideAnimation_numberOfIntervals
 var current_height=orginal_height;
 var k=setInterval(function(){current_height-=height_incre; if(current_height>comment_closedHeight)e.style.height=current_height+"px";else{e.style.height=comment_closedHeight+"px";clearInterval(k);window.comment_toggleFlag[c]="closed"};},window.slideAnimation_intervalDuration);
 document.getElementById('close_'+comment).style.display='none';
 document.getElementById('view_comment_'+comment).style.display='';
}

(function(){
 window.comment_height=new Object();
 window.comment_toggleFlag=new Object();
 window.comment_ids=new Array();
 var comment_minheight=window.comment_closedHeight;
 var els=document.getElementsByTagName("*");
  for(var i=0;i<els.length;i++){
  if(!/ comment_content /.test(" "+els[i].className+" "))continue;
//  els[i].style.overflow="hidden";
  window.comment_height[els[i].getAttribute("id")]=els[i].offsetHeight;
  window.comment_toggleFlag[els[i].getAttribute("id")]="closed";
  window.comment_ids[window.comment_ids.length]=els[i].getAttribute("id")
  els[i].style.height=comment_minheight+"px";
 }
})();
