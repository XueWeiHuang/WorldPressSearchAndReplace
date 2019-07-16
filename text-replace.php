<?php
/*
Plugin Name: HTML Text Replacement


Description: This plugin allows you to search text in the html page by text or by tag
Author: Joe Huang
License: MIT

*/

//Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function textreplace_plugin_meta( $links, $file ) { // add some links to plugin meta row
	if ( strpos( $file, 'html_text_replace.php' ) !== false ) {
		$links = array_merge( $links, array( '<a href="' . esc_url( get_admin_url(null, 'tools.php?page=html_text_replace') ) . '">Settings</a>' ) );
	}
	return $links;
}


function textreplace_add_pages() {
	$page = add_submenu_page( 'tools.php', 'HTML Text Replacement', 'HTML Text Replacement', 'activate_plugins', 'html_text_replace', 'textreplace_options_page' );
	add_action( "admin_print_scripts-$page", "textreplace_admin_scripts" );
}

function textreplace_options_page() {
	if ( isset( $_POST['setup-update'] ) ) {
		$_POST = stripslashes_deep( $_POST );

		if ( isset ( $_POST['findtextreplace'] ) && is_array( $_POST['findtextreplace'] ) ) { 
			foreach ( $_POST['findtextreplace'] as $key => $find ){
				if ( empty($find) ){ 
					unset( $_POST['findtextreplace'][$key] );
					unset( $_POST['replacetext'][$key] );
					unset( $_POST['replaceregex'][$key] );
					unset( $_POST['replacetext'][$key] );
				}
				if ( !isset( $_POST['replaceregex'][$key] ) ) {
					$_POST['findtextreplace'][$key] = str_replace( "\r\n", "\n", $find );
				}
			}
		}
		unset( $_POST['submit-import'] );
		if( empty( $_POST['findtextreplace'] ) ) {
			delete_option( 'replacetext_plugin_settings' );
		} else {
			update_option( 'replacetext_plugin_settings', $_POST );
		}
		echo '<div class="container">';
		echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
		echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
		echo   '<span aria-hidden="true">&times;</span>';
		echo '</button>';
		echo '<strong>Options Updated!</strong> ';
		echo '</div>';
		echo '</div>';
	}
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

<div class="container" >
	<h2 class="text-center"><strong>HTML Text Replacement</strong></h2>
	<h3>Select search type to begin. </p>
	
<div class="container">
    <div class="input-group-prepend">
      <div class="input-group-text row col-sm-12">
          <div  class="col-sm-4"><input  id="tagsearch" type="radio" name="searchType" >&nbsp Search by tag<br></div>
          <div class="col-sm-2"></div>
      <div  class="col-sm-4"><input id="textsearch" type="radio" name="searchType"  >&nbsp Search by text<br></div>
      </div>
    </div>
</div>
<hr>

<div class="container">
        <div class="col-sm-12 row">
			<div class="col-sm-3">
			<select id="tagdropdown" class="custom-select" id="inputGroupSelect01" size=10 disabled hidden>
			  <optgroup label = "Choose a tag">			  
              <option value="h1">h1</option>
              <option value="h2">h2</option>
              <option value="h3">h3</option>
              <option value="h4">h4</option>
              <option value="h5">h5</option>
              <option value="footer">footer</option>
            </select>
			</div>
         
  <div class="col-sm-9">
	<div id="textreplace-items">
		<form   method="post" action="<?php echo esc_url( $_SERVER["REQUEST_URI"] ); ?>">
			<input type="button" class="btn btn-info btn-small" value="Add Search & Replace Criteria" onClick="addFormField(); return false;" />
			<input type="submit" class="btn btn-success btn-small" value="Update Page" name="update" id="update" />
			<input type="hidden" name="setup-update" />
			<br>
			<br style="clear: both;" />
			<?php $textreplace_settings = get_option( 'replacetext_plugin_settings' ); ?>
			<ul id="textreplace_itemlist" class="list-group">
			<?php
				$i = 0;
				if ( isset ( $textreplace_settings['findtextreplace'] ) && is_array( $textreplace_settings['findtextreplace'] ) ){
					$i = 1;
					foreach ( $textreplace_settings['findtextreplace'] as $key => $find ){
						if( isset( $textreplace_settings['replaceregex'][$key] ) ) {
							$regex_checked = 'CHECKED';
						} else {
							$regex_checked = '';
						}

						if ( isset( $textreplace_settings['replacetext'][$key] ) ) {
							$textplace_replace = $textreplace_settings['replacetext'][$key];
						} else {
							$textplace_replace = '';
						}

						echo "<li id='row$i' class='list-group-item'>";					
						echo "<div class=' text-center'>";
						echo "<label class='col text-center' for='findtextreplace$i' style='font-size:15px'>Find:</label>";
						echo "<textarea class='col' name='findtextreplace[$i]' id='findtextreplace$i' style='font-size:15px'>". esc_textarea( $find ) ."</textarea>";
						echo "<br>";
						echo "<label class='col text-center' for='replacetext$i' style='font-size:15px'>Replace With</label>"    ;  
						echo "<textarea class='col' name='replacetext[$i]' id='replacetext$i' style='font-size:15px'>" . esc_textarea( $textplace_replace ). "</textarea>";
						echo "<input style='margin-right: 9px' type='button' class='btn btn-danger btn-small' value='Remove' onClick='removeFormField(\"#row$i\"); return false;' />";
						echo "<label class='side-label' for='replaceregex$i' style='font-size:15px' hidden hidden>Use RegEx:</label>";
						echo "<input  class='checkbox' type='checkbox' name='replaceregex[$i]' id='replaceregex$i' $regex_checked  hidden/>";
						echo "<input type='submit' class='btn btn-success btn-small' value='Update Page' name='update' id='update' />";
						echo "</div>";
						echo "</li>";
						unset($regex_checked);
						$i = $i + 1;
					}
				} 
				?>
			</ul>
			<div id="divTxt"></div>
		    <div class="clearpad"></div>
			<input type="button" class="btn btn-info btn-small" value="Add" onClick="addFormField(); return false;" />
			<input type="submit" class="btn btn-success btn-small" value="Update Page" name="update" id="update" />
		 	<input type="hidden" id="id" value="<?php echo $i; ?>" />
		</form>
		</div>
			</div>
		</div>
      </div>
</div>

<div class="container">
<h4><strong>This is general instruction:</strong></h4>
<ol>
<li>Select between two different search function: either search by tag or search by text.</li>
<li>Click "Add Search & Replace Criteria" to pop up new textarea to fill in the text you want to search within the html page.</li>
<li>If you want to change more than one field, simply click "Add" or "Add Search & Replace Criteria".</li>
<li>Once you are satisfied with your changes, click any "Update Page" to take changes effective.</li>
<li>If you choose to use "Search by tag" function, you will find there is not textare for you to type in the tag, as I already do it for you in the back. All you need to do is to fill the text you want to display
				in the available textarea between two tags. e.g. if you choose h1, and you want to show "Hello" as h1, you put the "Hello" between "<&#104;1>" and "<&#47;&#104;1>" as
				"<&#104;1>Hello<&#47;&#104;1>" .
<li>if you want to undo change, click remove and update the page. the changes will be reverted and page will restored to what it was in default</li>
</ol>
</div>

<script >

	document.getElementById("tagdropdown").addEventListener("change", ()=>{
		var selectedTag=document.getElementById("tagdropdown").value;
		document.getElementById("textreplace_itemlist").lastElementChild.getElementsByTagName("TEXTAREA")[0].hidden=true;
		document.getElementById("textreplace_itemlist").lastElementChild.getElementsByTagName("label")[0].textContent="Find: <"+selectedTag+">";
		document.getElementById("textreplace_itemlist").lastElementChild.getElementsByTagName("TEXTAREA")[0].value="/<"+selectedTag+"[^>]*>(.|"+"\u005C\u006E"+")*?<\u005C/"+selectedTag+">/";
		document.getElementById("textreplace_itemlist").lastElementChild.getElementsByClassName("checkbox")[0].checked=true;
		document.getElementById("textreplace_itemlist").lastElementChild.getElementsByTagName("TEXTAREA")[1].value="<"+selectedTag+"></"+selectedTag+">";
	})

    document.getElementById("textsearch").addEventListener("change", (evt) => {       
		document.getElementById("tagdropdown").disabled=true;
		document.getElementById("tagdropdown").hidden=true;		
    })
    document.getElementById("tagsearch").addEventListener("change", (evt)=>
    {
	   document.getElementById("tagdropdown").disabled=false;
	   document.getElementById("tagdropdown").hidden=false;	   
	})
		function addFormField() {
	var id = jQuery('#id').val();
		jQuery("#textreplace_itemlist").append(
			"<hr>"+		
			"<li id ='row" + id + "' class='list-group-item'>" +
					"<div class='text-center'>" +
					"<label class='col text-center' style='font-size:15px;' for='findtextreplace" + id + "'>Find:</label>" +
					"<textarea class='col' style='font-size:15px;' name='findtextreplace["+ id +"]' id='findtextreplace" + id + "'></textarea>" +
					"<br>" +
					"<label class='col text-center' style='font-size:15px;' for='replacetext" + id + "'>Replace With:</label>" +
					"<textarea class='col' style='font-size:15px;' name='replacetext["+ id +"]' id='replacetext" + id + "'></textarea>" +
					"<input style='margin-right: 9px' type='button' class='btn btn-danger btn-small' value='Remove' onClick='removeFormField(\"#row" + id + "\"); return false;' />\n" +
					"<label style='font-size:15px' class='side-label' for='replaceregex" + id + "' hidden>Use Regex:</label>" +
					"<input class='checkbox' type='checkbox' name='replaceregex[" + id + "]' id='replaceregex" + id +"'  hidden />" +
					"<input type='submit' class='btn btn-success btn-small' value='Update Page' name='update' id='update' />"+
				"</div>" +
			"</li>");
		id = (id - 1) + 2;
		document.getElementById("id").value = id;		
}

function removeFormField(id) {
	jQuery(id).remove();
}
	
jQuery(function() {
	jQuery( "#textreplace_itemlist" ).sortable();
});
</script>
<?php } ?>
<?php
function textreplace_admin_scripts() {

	wp_enqueue_script( 'jquery-ui-1', plugins_url() . '/html_text_replace/js/jquery-ui-1.10.3.custom.min.js', array('jquery') );
}
function textreplace_ob_call( $buffer ) { 
	
	$textreplace_settings = get_option( 'replacetext_plugin_settings' );
	if ( is_array( $textreplace_settings['findtextreplace'] ) ) {
		foreach ( $textreplace_settings['findtextreplace'] as $key => $find ) {
			if( isset( $textreplace_settings['replaceregex'][$key] ) ) {
				$buffer = preg_replace( $find, $textreplace_settings['replacetext'][$key], $buffer );
			} else {			
				$buffer = str_replace( $find, $textreplace_settings['replacetext'][$key], $buffer );
			}
		}
	}
	return $buffer;
}

function textreplace_template_redirect() {
	ob_start();
	ob_start( 'textreplace_ob_call' );
}

add_action( 'admin_menu', 'textreplace_add_pages' );

add_filter( 'plugin_row_meta', 'textreplace_plugin_meta', 10, 2 );

add_action( 'template_redirect', 'textreplace_template_redirect' );