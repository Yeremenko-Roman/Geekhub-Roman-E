jQuery(function($) {
	jQuery("input#ctoggleCheckboxes_menusubscriber, input#ctoggleCheckboxes_menucontributor, input#ctoggleCheckboxes_menuauthor, input#ctoggleCheckboxes_menu, input#ctoggleCheckboxes_menuadm, input#ctoggleCheckboxes_postsubscriber, input#ctoggleCheckboxes_postcontributor, input#ctoggleCheckboxes_postauthor, input#ctoggleCheckboxes_post, input#ctoggleCheckboxes_postadm, input#ctoggleCheckboxes_pagesubscriber, input#ctoggleCheckboxes_pagecontributor, input#ctoggleCheckboxes_pageauthor, input#ctoggleCheckboxes_page, input#ctoggleCheckboxes_pageadm").css("display", "none");
});

var bAllChecked = false;
function toggleCheckboxes_menusubscriber() {
	jQuery("input[id^='check_menusubscriber']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_menusubscriber').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_menucontributor() {
	jQuery("input[id^='check_menucontributor']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_menucontributor').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_menuauthor() {
	jQuery("input[id^='check_menuauthor']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_menuauthor').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_menu() {
	jQuery("input[id^='check_menu']").not("input[id^='check_menuadm']").not("input[id^='check_menuauthor']").not("input[id^='check_menucontributor']").not("input[id^='check_menusubscriber']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_menu').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_menuadm() {
	jQuery("input[id^='check_menuadm']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_menuadm').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

//post
var bAllChecked = false;
function toggleCheckboxes_postsubscriber() {
	jQuery("input[id^='check_postsubscriber']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_postsubsciber').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_postcontributor() {
	jQuery("input[id^='check_postcontributor']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_postcontributor').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_postauthor() {
	jQuery("input[id^='check_postauthor']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_postauthor').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_post() {
	jQuery("input[id^='check_post']").not("input[id^='check_postsubscriber']").not("input[id^='check_postcontributor']").not("input[id^='check_postauthor']").not("input[id^='check_postadm']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_post').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_postadm() {
	jQuery("input[id^='check_postadm']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_postadm').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

//page
var bAllChecked = false;
function toggleCheckboxes_pagesubscriber() {
	jQuery("input[id^='check_pagesubscriber']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_pagesubscriber').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_pagecontributor() {
	jQuery("input[id^='check_pagecontributor']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_pagecontributor').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_pageauthor() {
	jQuery("input[id^='check_pageauthor']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_pageauthor').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_page() {
	jQuery("input[id^='check_page']").not("input[id^='check_pagesubscriber']").not("input[id^='check_pagecontributor']").not("input[id^='check_pageauthor']").not("input[id^='check_pageadm']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_page').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}

var bAllChecked = false;
function toggleCheckboxes_pageadm() {
	jQuery("input[id^='check_pageadm']").attr("checked", !bAllChecked);
	bAllChecked = !bAllChecked; jQuery('#atoggleCheckboxes_pageadm').text( bAllChecked ? adminimizeL10n.none : adminimizeL10n.all );
}