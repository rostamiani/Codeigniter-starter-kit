################################
# Programmer notes             #
# Mohammad Mahdi Rostamiani    #
# rostamiani@gmail.com         #
# 09364286628                  #
################################

1.Twig template variables:

- no_menu: if exists, there is no navigation

- alert(object): Shows a modal alert at page load
	alert properties:
		-text : The message text
		-type : Bootstrap color classes (success|danger|info|warning|primary|secondary)

- alert_box(array of objects): Shows a couple of messages as a colored boxes at top of the screen
	alert_box properties:
		-text : The message text
		-type : Bootstrap color classes (success|danger|info|warning|primary|secondary)

	You can user sessions to add alerts too. Just add alert to $_SESSION['alert_box]
	Or using add_alert($text, $type) helper function

- page_header(string): The text at header and the top of each page

- top_buttons(array of objects): An array of object to add buttons to the top of the page
	button properties:
		-title : Button title
		-link : Button hyperlink
		-type : Bootstrap color classes (success|danger|info|warning|primary|secondary)
		-icon : Button icon as Font Awesome class (Example 'fas fa-plus-square')
		-class : Some additional classes for the button
		-attr : Additional attributes for button such as id, class and etc

-breadcrumb(array of objects): Adds a breadcrumb on top of the page
	breadcrumb properties:
		-title : Title of breadcrumb item
		-url : Url of breadcrumb item
		-active : Add this property for breadcrumb of current page

2.Template Blocks
	
	-content: Tha main content of the page
	-css_files: link tags to place at header of the page
	-js_files: script tags consist of external or internal Javascript codes

3.Javascript functions:

	-modal_message(message, type="success", callback=function(){})
	Shows a message as modal. Callback is a function that will be triggered on ok

	-modal_confirm(message, callback=function(){})
	Shows a modal with 'yes/no' buttons. Callback wii be fired when 'yes' button clicked.

4.Authentication
	
	To restrict user acces to some user types, add this line to any function of a controller
	or __construct to restrict all.

	- $this->auth->just_for({user_type});
	user_type(string or array): a single user type or an array of allowed user types

	- Example:
		$this->auth->just_for(['user','admin','manager']);

5.Labels

	Add list labels in application/config/Labels.php with this format:

	$config['label']['name'] = 'نام';

	labels can be arrays:

	$config['label']['status'] = 
	[
		0 => 'فعال',
		1 => 'غیر فعال',
		2 => 'حذف شده'
	];