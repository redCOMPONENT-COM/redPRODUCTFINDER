jQuery(document).ready(function(){
	addBinding();

	jQuery('#redproductfinder_filter').on('click', 'input.filter-tag', function(){
		var toExpand = getExpandedMenus();
		getProducts('', toExpand);
	});

	jQuery("#productlist").on('click', '.pagination-links a', function(ev){
		ev.preventDefault();
		getProducts(jQuery(this).attr('href'));
	});

	jQuery('.redproductfilter input[type="checkbox"]').on('change', function(){
		var thisCheck = jQuery(this);
		if (thisCheck.is(':checked')) {
			thisCheck.attr('checked', 'checked');
		}else{
			thisCheck.attr('checked', false);
		}
	});
});

// Restore previously checked items
jQuery(window).load(function(){
	jQuery('.redproductfilter input[type="checkbox"]').each(function() {
		if (jQuery(this).attr('checked')) {
			jQuery(this).attr('checked', 'checked');
		}else{
			jQuery(this).attr('checked', false);
		}
	});
});

// Forces onload function to run, even when coming to the page using the back button
jQuery(window).unload(function(){});

function redproductfinderShowMore(id,e){
	var Element = jQuery('.redproductfilter_tag_wrapper div[data-parent="'+id+'"]');
	redBOX.open(
		jQuery("#redproductfilter_popup")[0],
		{
			handler: 'clone',
			size: {x: 650, y: 250},
			onOpen: function(){
				var html = '<p><h3 id="redproductfilterPopupTitle">&nbsp;</h3></p><table id="redproductfilterPopupTable" style="width:100%"><tr>';
				var max = Element.length;
				Element.each(function(index){
					html += '<td>';
					html += jQuery(this).html();
					html += '</td>';
					if((index+1)%3 == 0){
						html += '</tr><tr>';
					}
					if(index+1 == max){
						html += '</tr></table>';
						jQuery("#sbox-content > div").html(html);
						jQuery("#sbox-content > div").css({"padding":"10px"});
						jQuery("#sbox-content #redproductfilterPopupTitle").text(jQuery('.redproductfilter_tag_wrapper div[data-title="'+id+'"]').text());
						jQuery("#sbox-content #redproductfilterPopupTable input").css({"float":"left","margin-right":"10px"});
						jQuery("#sbox-content #redproductfilterPopupTable label").click(function(){ var E=jQuery(this).parent().find('input'); E[0].checked=(!E[0].checked); });
						jQuery("#sbox-content #redproductfilterPopupTable input").click(function(){ jQuery(".redproductfilter_type #"+jQuery(this).attr('data-id')).click(); });
						jQuery("#sbox-content #redproductfilterPopupTable tr td").css({"width":"33%"});
					}
				});
			}
		}
	);
	return false;
}

function addBinding()
{
	try
  	{
  		var lazyloader = new LazyLoad();
  	}
	catch(err){}
}

function getProducts(paginationUrl, toExpandMenus)
{
	var options = new Object();
	var useFilter = false;
	var arrayValues = new Object();
	jQuery('input[id*=redproductfiltertag_]').each(function(){
		if(jQuery(this).is(':checked')) {
			useFilter = true;
			var info = jQuery(this).attr('rel').split('=');
			var nameStack = info[0].replace('[]', '');
			if (typeof arrayValues[nameStack] === 'undefined') {
				arrayValues[nameStack] = [];
			}
			arrayValues[nameStack][arrayValues[nameStack].length] = info[1];
		}
	});



	if(!useFilter)
	{
		options.nofilter = 1;
	}else{
		jQuery.each(arrayValues, function(index, value) {
			options[index] = value;
		});
	}

	options.texpricemin = jQuery("#filter_min").val();
	options.texpricemax = jQuery("#filter_max").val();
	options.cid = jQuery("#catid").val();
	options.mid = jQuery("#maid").val();
	options.paginationUrl = paginationUrl;
	options.endlimit = jQuery("#endlimit").val();
	options.Itemid = jQuery("#Itemid").val();

	loadProductsFinder(options, toExpandMenus);
}

function loadProductsFinder(options, toExpandMenus)
{
	jQuery('.redproductfilter_loading').show();
	var title = jQuery('.componentheading');
	jQuery.ajax({
		'url' : site_url + "index.php?option=com_redproductfinder&task=findproducts&view=redproductfinder&layout=searchresult_ajax&format=ajax",
		'type': 'post',
		'data' : options,
		success: function(data){
			var temp = data.split("@@@@@");
			jQuery("#productlist").html(temp[0]).prepend('<div style="clear:both"></div>').prepend(title);
			jQuery(".category_pagination").html(temp[1]);
			jQuery("#redproductfinder_filter").html(temp[2]);
			jQuery('.redproductfilter_loading').hide();

			addBinding();
			jQuery.each(toExpandMenus, function(index, value) {
				showOrHide(jQuery('#' + value).find('.expand').get(0));
			});
		}
	});
}

function showOrHide(group) {
	groupDiv = group.parentNode;
	tagProperties = jQuery(groupDiv).find('.expand');
	if (!jQuery(groupDiv).find('.expand:first').is(':visible')) {
		tagProperties.show();
		jQuery(group).html(jQuery(group).html().replace('\u25BA', '\u25BC'));
	}else{
		tagProperties.hide();
		jQuery(group).html(jQuery(group).html().replace('\u25BC', '\u25BA'));
	}
}

function getExpandedMenus() {
	var menuArray = [];
	jQuery('.expand:visible').each(function() {
		var menuRoot = jQuery(this).parent();
		if (jQuery.inArray(menuRoot.attr('id'), menuArray) == -1) {
			menuArray.push(menuRoot.attr('id'));
		}
	});
	return menuArray;
}