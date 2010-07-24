$().ready(function() {

  $('#ex3a').jqm({
    trigger: 'a.ex3aTrigger',
    overlay: 60, /* 0-100 (int) : 0 is off/transparent, 100 is opaque */
    overlayClass: 'greyOverlay',
    modal: true, /* FORCE FOCUS */
    onHide: function(h) {
      h.o.remove(); // remove overlay
      h.w.fadeOut(444);
     //$('object').show();
    },// hide window
 	onShow: function(h) {
	  //$('object').hide();
 	  h.w.fadeIn(444);
 	}//show window
 })
 
 	$('#edit').click(function() {
 		$('#title').html('Edit Comment');
 		tinyMCE.setContent('<p>Edit Comment</p>');
 	});
 	
 	$('a.add').click(function() {
 		$('#title').html('Add Comment');
 		tinyMCE.setContent('');
 	});
 	
 	$('a.quoteComment').click(function() {
 		$('#title').html('Add Comment');
 		$.ajax({
    		url: 'itemBody.php',
    		type: 'GET',
    		data: 'itemId='+$(this).attr('id'),
    		dataType: 'html',
    		timeout: 1000,
    		error: function(){
        		alert('Error connecting to the server, check connection and try again');
        		$('#title').html('Error Getting Quote');
   		 	},
    		success: function(html){
        		// do something with html
        		tinyMCE.setContent(html);
    		}
		});
 	});
 	
    $('#save').click(function() {
    	$('#title').html('Saving...');
		$.ajax({
    		url: 'save_comment.php',
    		type: 'POST',
    		dataType: 'html',
    		timeout: 1000,
    		error: function(){
        		alert('Error connecting to the server, check connection and try again');
        		$('#title').html('Error Saving');
   		 	},
    		success: function(html){
        		// do something with html
        		alert('Success' + html);
        		$('#title').html('Data Saved');
        		$('#ex3a').jqmHide();
    		}
		});
   })
    .jqDrag('.jqDrag'); /* make dialog draggable, assign handle to title */
  
  // Close Button Highlighting. IE doesn't support :hover. Surprise?
  $('input.jqmdX')
  .hover(
    function(){ $(this).addClass('jqmdXFocus'); }, 
    function(){ $(this).removeClass('jqmdXFocus'); })
  .focus( 
    function(){ this.hideFocus=true; $(this).addClass('jqmdXFocus'); })
  .blur( 
    function(){ $(this).removeClass('jqmdXFocus'); });
    
});