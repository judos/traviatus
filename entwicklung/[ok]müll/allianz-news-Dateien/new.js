/* Dynamic images, change class */
window.addEvent('domready', function() {

	$$('*.dynamic_img').addEvents({
	    'mouseenter': function(){
	        this.addClass('over');
	    },
	    'mouseleave': function(){
	        this.removeClass('over');
	        this.removeClass('clicked');
	    },
	    'mousedown': function(){
	    	this.removeClass('over');	    	
	    	this.addClass('clicked');
	    }
	});
	
	$$('img.tSwitch').addEvents({
		'mousedown': function(){
			var tbody = this.getParent('thead').getNext('tbody');			
			tbody.toggleClass('hide');
			if (tbody.hasClass('hide')) {
				document.cookie = 't3' + this.getParent('table').getProperty('id') + '=1; expires=Wed, 1 Jan 2020 00:00:00 GMT';
				this.removeClass('opened');
				this.addClass('closed');
			} else {
				document.cookie = 't3' + this.getParent('table').getProperty('id') + '=1; expires=Thu, 01-Jan-1970 00:00:01 GMT';
				this.removeClass('closed');
				this.addClass('opened');				
			}
		}		
	});
	
	$$('table.row_table_data tbody tr').addEvents({
	    'mouseenter': function(){
	        this.addClass('hlight');
	    },
	    'mouseleave': function(){
	        this.removeClass('hlight');
	    },
	    'mousedown': function(){
	    	this.toggleClass('marked');
	    }
	});
	
	
});