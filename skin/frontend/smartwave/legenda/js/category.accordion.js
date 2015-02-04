function categoryAccordion() {
    var plusIcon = '+';
    var minusIcon = '&ndash;';
    var etCats = jQuery('.product-categories');
    var openerHTML = '<div class="open-this">' + plusIcon + '</div>';
    jQuery('.open-this').remove();
    etCats.find('>li').has('.children').has('li').addClass('parent-level0').prepend(openerHTML);
    if (jQuery('.current-cat').length > 0) {
        jQuery('.current-cat').parents('li.cat-item').addClass('current-cat-parent');
    }
    if (jQuery('.current-cat.parent-level0, .current-cat-parent').length > 0) {
        jQuery('.current-cat.parent-level0, .current-cat-parent').find('.open-this').html(minusIcon).parent().addClass('opened').find('ul.children').show();
    } else {
        etCats.find('>li').first().find('.open-this').html(minusIcon).parent().addClass('opened').find('ul.children').show();
    }
    etCats.find('.open-this').click(function () {
        if (jQuery(this).parent().hasClass('opened')) {
            jQuery(this).html(plusIcon).parent().removeClass('opened').find('ul.children').slideUp(200);
        } else {
            jQuery(this).html(minusIcon).parent().addClass('opened').find('ul.children').slideDown(200);
        }
    });
}
jQuery(document).ready(function($){
    categoryAccordion();
});