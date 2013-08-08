$(function(){

  //set height for list section and scroll section
	function setHeight(){
		var eleHeight = $('body').height() - 145 - $('#footer').height(),
        subMenu = eleHeight - 129;
        
	  $('.widget-box.rss-list').height(eleHeight);
	  $('#sidebar li.submenu ul').css({'max-height':subMenu});
	}
	
	setHeight();
	$(window).resize(function(){
		setHeight();
	});
  
  //set selection indicator
  var path=location.pathname+location.search;
  $('#sidebar a').parents('li').removeClass('active');
  $('#sidebar a[href="'+path+'"]').parents('li').addClass('active');
  
  
	var isloading=false, noMoreData=false, url, offset=25, limit=25;
	$('.widget-box').scroll(function(){      
		if($(this).scrollTop() + $(this).innerHeight() + 200 >= $(this)[0].scrollHeight && !noMoreData && !isloading)
		{
			var l=location;
				url = l.protocol+'//'+l.host+l.pathname+l.search;
				url += '&offset='+offset;
				hash = l.hash;
			
			if(hash==='#show-unread'){
				url += '&unreadOnly=1';
			}
				
			loadData(url);
		}
	});
	
	function loadData(url)	{                     
		//url = window.location.href;
		//url += '&offset='+offset;
		$.ajax({
            url : url,
            beforeSend: function(){
            	isloading = true;
            },
            success : function(data){
                if(!data){
                	noMoreData = true;
                }
                else{
                    $("#entries").append(data);
                    offset += limit;
                }
                isloading = false;
           }
		});
	}

	//add or remove favorites
	$('#entries').on('click', '.star', function(e){
		e.preventDefault();
		e.stopPropagation();
		var item = $(this),
			item_id = $(this).parents('.entry').attr('id'),
			stars = $(this).parents('.entry').find('.star');
			isActive = $(this).hasClass('icon-star'),
			path = window.location.origin+window.location.pathname;
		if(isActive){    //this is starred item. remove star
			var url = path+'?r=site/RemoveFavorite&item_id='+item_id;
		}else{        //add star
			var url = path+'?r=site/AddFavorite&item_id='+item_id;
		}

		$.ajax({
	        url : url,
	        success : function(data){        
	            if(data){
	            	$.each(stars, function(k, v){
	            		if(isActive){     
		                    $(v).removeClass('icon-star');
		                    $(v).addClass('icon-star-empty');
		                }else{
		                    $(v).removeClass('icon-star-empty');
		                    $(v).addClass('icon-star');
		                }
	            	});
	            }
	        }
		});
	});
  
	/* get entry details */
	$('#entries').on('click', '.entry .collapsed', function(){
		//first remove all entry container and entry action from entry container
		//search for the template and add it to this class
		var parent = $(this).parents('.entry');
		if($(parent).hasClass('expand')){	//the entry is open proceed to close it
			$(parent).children('.entry-container, .entry-actions').remove();
			$(parent).removeClass('expand');
		}
		else{	
			//this is a collapsed entry some other entry might be open
			//try to close that entry by removing all entry-container 
			//and entry action elements
			var open_entry = $('#entries .entry.expand'),
				id=$(parent).attr('id');
			if(open_entry){
				$(open_entry).removeClass('expand');
				$(open_entry).find('.entry-container, .entry-actions').remove();	
			}
			
			//entry is collapsed expand it
			$(parent).addClass('expand');
			getEntryData($(parent), id);
			if($(parent).height()>500)
				$('.widget-box.rss-list').scrollTo('#'+id);
		}
	});
	
	function getEntryData(parent, itemId){	
		//first get the template
		var template = $('#template-item-container').html(), 
			isRead = $(parent).hasClass('read');
		
		//now get contents from db
		var path = window.location.origin+window.location.pathname,
			url = path+'?r=site/getItemData&item_id='+itemId+'&isRead='+isRead;
		$.ajax({
	        url : url,
	        cache:true,
	        async:false,
	        success : function(data){
	    		//merge the template and contents
	        	var contents = JSON.parse(data);
	        	$.each(contents, function(k, v){
	        		template = template.replace(':'+k, v);
	        	});
	        	
	        	//check for the star attribute
	        	if($(parent).find('.collapsed .star').hasClass('icon-star')){
	        		template = template.replace('icon-star-empty', 'icon-star');
        		}
	        	
	        	$(parent).append(template);
	        	
	        	//check for author name
	        	if(contents.author == ''){
	        		$(parent).find('.entry-author-parent').remove();
	        	}
	        	
	        	//if item is unread mark it as read
	        	if(!isRead){
	        		$(parent).addClass('read');
	        	}
	        }
		});
	}
	
	
	//set reload button
	$('#user-nav').on('click', '#reload', function(){
		window.location.reload();
		/*
		var url = $(this).attr('data-url');
		$.ajax({
	        url : url,
	        success : function(data){
	            alert(data);
	        }
		});
		*/
	});
	
	function loadPageVar (sVar) {
		return unescape(window.location.search.replace(
			new RegExp("^(?:.*[&\\?]" + escape(sVar).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1")
		);
	}
	
	/* mark as read. */
	$('#user-nav').on('click', '#mark_read', function(e){
		e.preventDefault();
		e.stopPropagation();
		var favorites=loadPageVar('favorites'), 
			channel=loadPageVar('channel_id'),
			url = $(this).attr('href'),
			id;
		
		if(!channel){	//channel_id not present
			if(!favorites){	//favorites not present
				id='0';	//this is in all items
			}else{
				id='-1';	//this is for favorites
			}
		}else{
			id=channel;		//this is for channel id
		}

		$.ajax({
	        url : url,
	        data:{'id':id},
	        success : function(data){
	            if(data){
	            	//find all unread lines and mark them as read
	            	var unread_items = $('#entries .entry:not(".read")');
	            	$.each(unread_items, function(){
	            		$(this).addClass('read');
	            	});
	            }
	        }
		});
	});
	/* mark as read. */
	
	/* view read or all items */
	$('#user-nav #view-filter').on('click', 'a', function(){
		var hash = $(this).attr('href'),
			l=location,
			url = l.protocol+'//'+l.host+l.pathname+l.search;
		
		if(hash==='#show-unread'){
			url += '&unreadOnly=1';
		}
		
		//clear all data in #entries container
		$('#entries .entry').remove();

		//get new data
		loadData(url);
	});
	/* view read or all items */
	
});