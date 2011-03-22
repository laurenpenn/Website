jQuery(document).ready(function () {
	
	/*
	 * Get an author's name from their ID
	 * Iterates through the coauthors-select input box until finds the entry with the associated ID
	 * @param int User ID
	 *
	 */
	 /*
	function coauthors_get_author_name( authorID ) {
		var select = jQuery('#coauthors-select');
		
		if(authorID){
			//Find the provided author ID
			var name = "";
			select.find("option").each(function(i){
				if(this.value == authorID) {
					name = this.innerHTML;
					return;
				}
	
			});
			return name;
		}
		return false;
	
	}
	*/
	
	/*
	 * Selects author (specified by authorID) within the appropriate select box (specified by selectID)
	 * @param int
	 * @param string ID of the select box
	 */
	function coauthors_select_author( author, selectID ) {
		if(!selectID) selectID = '#post_author_override';
	
		var select = jQuery(selectID);
		
		if(author.id){
			//Find the provided author ID
			select.find("option").each(function(i){
				if(this.value == author.id) {
					jQuery(this).attr('selected','true');
					// Fix to retain order of selected coauthors
					//jQuery(this).appendTo(select);
					return false;
				}
			});
		}
	
	}
	
	/*
	 * Unselects author
	 * @param string Name of the Author to remove
	 */
	function coauthors_remove_author( authorName ) {
		var select = jQuery('#coauthors-select');
		if(authorName){
			//Find the provided author ID
			select.find("option").each(function(i){
				if(this.innerHTML == authorName) {
					jQuery(this).removeAttr('selected');
					return true;
				}
			});
		}
	}
	
	/*
	 * Click handler for the delete button
	 * @param event
	 */
	var coauthors_delete_onclick = function(event){
		
		if(confirm(i18n.coauthors.confirm_delete)) {
			var tr = jQuery(this).parent().parent();
			var name = tr.find('span.author-tag').text();
			coauthors_remove_author(name);
			tr.remove();
			
			return true;
		}
		return false;
	};
	
	var coauthors_edit_onclick = function(event) {
		var tag = jQuery(event.currentTarget);
		
		var co = tag.prev();
								
		tag.hide();
		co.show()
			.focus()
			;
		
		co.previousAuthor = tag.text();
	}
	
	/*
	 * Save coauthor
	 * @param int Author ID
	 * @param string Author Name
	 * @param object The autosuggest input box
	 */
	function coauthors_save_coauthor(author, co) {
	
		coauthors_remove_author(co.next().text());
		
		// get sibling <span>, update, and hide
		co.next()
			.html(author.name)
			.show()
			.next()
				.val(author.login)
			;

		// select new author
		if(co.attr('name')=='coauthors-main') {
			coauthors_select_author( author );
		}
	
	}
	
	
	/*
	 * Add coauthor
	 * @param int Author ID
	 * @param string Author Name
	 * @param object The autosuggest input box
	 * @param boolean Initial set up or not?
	 */
//	function coauthors_add_coauthor(authorID, authorName, co, init, count){
	function coauthors_add_coauthor(author, co, init, count){
		
		// Check if editing
		if(co && co.next().attr('class')=='author-tag') {
			coauthors_save_coauthor(author, co);
		
		} else {
			// Not editing, so we create a new author entry
			if(count == 0) {
				var coName = (count == 0) ? 'coauthors-main' : '';
				// Add new author to <select>
				coauthors_select_author( author );
				var options = {};
			} else {
				var options = { addDelete: true, addEdit: false };
			}
			// Create autosuggest box and text tag
			if(!co) var co = coauthors_create_autosuggest(author, coName)
			var tag = coauthors_create_author_tag(author);
			var input = coauthors_create_author_hidden_input(author);

			coauthors_add_to_table(co, tag, input, options);
							
			if(!init) {
				// Create new author-suggest and append it to a new row
				var newCO = coauthors_create_autosuggest('', false);
				coauthors_add_to_table(newCO);
			}
		}
		
		co.bind('blur', coauthors_stop_editing);
		
		// Set the value for the auto-suggest box to the Author's name and hide it
		co.val(unescape(author.name))
		  .hide()
		  .unbind('focus')
		  ;
		
		return true;
	}
	
	
	/*
	 * Add the autosuggest box and text tag to the Co-Authors table
	 * @param object Autosuggest input box
	 * @param object Text tag
	 * @param 
	 */
	function coauthors_add_to_table( co, tag, input, options ) {
		if(co) {
			var td = jQuery('<td></td>')
						.addClass('suggest')
						.append(co)
						.append(tag)
						.append(input)
						;
			var tr = jQuery('<tr></tr>');
			tr.append(td);
	
			//Add buttons to row
			if(tag) coauthors_insert_author_edit_cells(tr, options);
			
			jQuery('#coauthors-table').append(tr);
		}
	}
	
	/* 
	 * Adds a delete and edit button next to an author
	 * @param object The row to which the new author should be added
	 */
	function coauthors_insert_author_edit_cells(tr, options){

		var td = jQuery('<td></td>')
					.addClass('coauthors-author-options')
					;

		/*
		if(options.addEdit) {
			var editBtn = jQuery('<span></span>')
							.addClass('edit-coauthor')
							.text(i18n.coauthors.edit_label)
							.bind('click', coauthors_edit_onclick)
							;
			td.append(editBtn);
		}
		*/
		if(options.addDelete) {
			var deleteBtn = jQuery('<span></span>')
								.addClass('delete-coauthor')
								.text(i18n.coauthors.delete_label)
								.bind('click', coauthors_delete_onclick)
								;
			td.append(deleteBtn);
		}
		
		jQuery(tr).append(td);
		return tr;		
							
	}
	
	/*
	 * Creates autosuggest input box
	 * @param string [optional] Name of the author
	 * @param string [optional] Name to be applied to the input box
	 */
	function coauthors_create_autosuggest(authorName, inputName) {
	
		if(!inputName) inputName = 'coauthorsinput[]';
	
		var co = jQuery('<input/>')
								.attr({
									'class': 'coauthor-suggest',
									'name': inputName
									})
								.appendTo(div)
								
								.suggest(coauthor_ajax_suggest_link,{
									onSelect: 
										function() {
											
											var vals = this.value.split("|");
											
											var author = {}
											author.id = jQuery.trim(vals[0]);										
											author.login = jQuery.trim(vals[1]);
											author.name = jQuery.trim(vals[2]);
											
											if(author.id=="New") {
												//alert('Eventually, this will allow you to add a new author right from here. But it\'s not ready yet. *sigh*');
												coauthors_new_author_display(name);
											} else {
												//coauthors_add_coauthor(login, name, co);
												coauthors_add_coauthor(author, co);
											}
										}
								})
								.keydown(function(e) {
									if(e.keyCode == 13) {return false;}
	
								})
								;
									
		if(authorName)
			co.attr('value', unescape(authorName));
		else
			co.attr('value', i18n.coauthors.search_box_text)
						.focus(function(){co.val('')})
						.blur(function(){co.val(i18n.coauthors.search_box_text)})
						;
		
		return co;
	
	}
	
	/*
	 * Blur handler for autosuggest input box
	 * @param event
	 */
	function coauthors_stop_editing(event) {
	
		var co = jQuery(event.target);
		var tag = jQuery(co.next());
		
		co.attr('value',tag.text());
	
		co.hide();
		tag.show();
		
	//	editing = false;
	}
	
	/*
	 * Creates the text tag for an author
	 * @param string Name of the author
	 */
	function coauthors_create_author_tag (author) {
	
		var tag = jQuery('<span></span>')
							.html(unescape(author.name))
							.attr('title', i18n.coauthors.input_box_title)
							.addClass('author-tag')
							// Add Click event to edit
							.click(coauthors_edit_onclick);
		return tag;
	}
	
	/*
	 * Creates the text tag for an author
	 * @param string Name of the author
	 */
	function coauthors_create_author_hidden_input (author) {
		var input = jQuery('<input />')
						.attr({
							'type': 'hidden',
							'id': 'coauthors_hidden_input',
							'name': 'coauthors[]',
							'value': unescape(author.login)
							})
						;
		
		return input; 
	}
	
	
	/*
	 * Display form for creating new author
	 * @param string Name of the author
	 */
	function coauthors_new_author_display (name) {
	
		tb_show('Add New User', '?inlineId=awesome&modal=true');
	
	}
	
	/*
	 * Creates display for adding new author
	 * @param string Name of the author
	 */
	function coauthors_new_author_create_display ( ) {
	
		var author_window = jQuery('<div></div>')
								.appendTo(jQuery('body'))
								.attr('id','new-author-window')
								.addClass('wrap')
								.append(
									jQuery('<div></div>')
										.addClass('icon32')
										.attr('id','icon-users')
									)
								.append(
									jQuery('<h2></h2>')
										.text('Add new author')
										.attr('id', 'add-new-user')
	
									)
								.append(
									jQuery('<div/>')
										.attr('id', 'createauthor-ajax-response')
									)
								;
		
		var author_form	= jQuery('<form />')
							.appendTo(author_window)
							.attr({
								id: 'createauthor',
								name: 'createauthor',
								method: 'post',
								action: ''
							})
							;
		
		
		
		var create_text_field = function( name, id, label) {
			
			var field = jQuery('<input />')
							.attr({
								type:'text',
								name: name,
								id: id,
							})
			var label = jQuery('<label></label>')
							.attr('for',name)
							.text(label)
							
			//return {field, label};
				
		};
		
		create_field('user_login', 'user_login', 'User Name');
		create_field('first_name', 'first_name', 'First Name');
		
		//last_name
		//email
		//pass1
		//email password checkbox
		//role
	}
	
	
	
	if(jQuery('#post_author_override')){
		
		// Check if user has permissions to change post authors; if not, remove controls and end
		if(!coauthors_can_edit_others_posts){
			jQuery('#authordiv, #pageauthordiv').remove();
			return;
		}
	
		// Changes the meta_box title from "Post Author" to "Post Author(s)"
		var h3 = jQuery('#authordiv :header, #pageauthordiv :header').html(
				/page[^\/]+$/.test(window.location.href) ?
					i18n.coauthors.page_metabox_title
				:
					i18n.coauthors.post_metabox_title
		);
		
		// Add the controls to add co-authors
		var div = jQuery('#authordiv div, #pageauthordiv div').filter(function(){
			if(jQuery(this).is('.inside') || jQuery(this).is('.dbx-content'))
				return true;
			return false;
		})[0];
		
		if(div){
			
			// Create the co-authors table
			var table = jQuery('<table></table>')
									.attr('id', 'coauthors-table')
									;
			var coauthors_table = jQuery('<tbody></tbody>')
											.appendTo(table)
											;
			var tr = jQuery('<tr></tr>');
			var td = jQuery('<td></td>')
								.addClass('select')
								;
			
			var select = jQuery('#post_author_override')[0];
					
			td.append(select);
			tr.append(td);
			coauthors_table.append(tr);
			jQuery(div).append(table);
	
			// Hide original dropdown box
			jQuery('#post_author_override').hide();
			
			// Show help text
			var help = jQuery('<p></p>').html(i18n.coauthors.help_text);		
			jQuery('#authordiv .inside').append(help);
			jQuery('#pageauthordiv .inside').append(help);
	
		}
	
		// Select authors already added to the post
		var addedAlready = [];
		//jQuery('#the-list tr').each(function(){
		var count = 0;
		jQuery.each(post_coauthors, function() {
			coauthors_add_coauthor(this, undefined, true, count );
			count++;
		});
	
		// Create new author-suggest and append it to a new row
		var newCO = coauthors_create_autosuggest('', false);
		coauthors_add_to_table(newCO);
	}

});
