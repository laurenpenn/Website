var _gformPriceFields = new Array();

function gformIsHidden(element){
    return element.parents('.gfield').css("display") == "none";
}

var _anyProductSelected;
function gformCalculateTotalPrice(formId){

    if(!_gformPriceFields[formId])
        return;

    var price = 0;

    _anyProductSelected = false; //Will be used by gformCalculateProductPrice().
    for(var i=0; i<_gformPriceFields[formId].length; i++){
        price += gformCalculateProductPrice(formId, _gformPriceFields[formId][i]);
    }

    //add shipping price if a product has been selected
    if(_anyProductSelected){
        //shipping price
        var shipping = gformGetShippingPrice(formId)
        price += shipping;
    }
    //updating total
    var totalElement = jQuery("#ginput_total_" + formId);
    if(totalElement.length > 0){
        totalElement.next().val(price);
        totalElement.html(gformFormatMoney(price));
    }
}

function gformGetShippingPrice(formId){
    var shippingField = jQuery(".gfield_shipping_" + formId + " input[type=hidden], .gfield_shipping_" + formId + " select, .gfield_shipping_" + formId + " input:checked");
    var shipping = 0;
    if(shippingField.length == 1 && !gformIsHidden(shippingField)){
        if(shippingField.attr("type").toLowerCase() == "hidden")
            shipping = shippingField.val();
        else
            shipping = gformGetPrice(shippingField.val());
    }

    return gformToNumber(shipping);
}

function gformCalculateProductPrice(formId, productFieldId){
    var price = gformGetBasePrice(formId, productFieldId);

    var suffix = "_" + formId + "_" + productFieldId;

    //Drop down auto-calculating labels
    jQuery(".gfield_option" + suffix + " select, .gfield_shipping_" + formId + " select").each(function(){
        var selected_price = gformGetPrice(jQuery(this).val());
        jQuery(this).children("option").each(function(){
            var label = gformGetOptionLabel(this, jQuery(this).val(), selected_price);
            jQuery(this).html(label);
        });
    });

    //Checkboxes labels with prices
    jQuery(".gfield_option" + suffix + " .gfield_checkbox input").each(function(){
        var element = jQuery(this).next();
        var label = gformGetOptionLabel(element, jQuery(this).val(), 0);
        element.html(label);
    });

    //Radio button auto-calculating lables
    jQuery(".gfield_option" + suffix + " .gfield_radio, .gfield_shipping_" + formId + " .gfield_radio").each(function(){
        var selected_price = 0;
        var selected_value = jQuery(this).find("input:checked").val();
        if(selected_value)
            selected_price = gformGetPrice(selected_value);

        jQuery(this).find("input").each(function(){
            var label_element = jQuery(this).next();
            var label = gformGetOptionLabel(label_element, jQuery(this).val(), selected_price);
            label_element.html(label);
        });
    });

    jQuery(".gfield_option" + suffix + " input:checked, .gfield_option" + suffix + " select, .gfield_donation_" + formId + "_" + productFieldId + " select").each(function(){
        if(!gformIsHidden(jQuery(this)))
            price += gformGetPrice(jQuery(this).val());
    });

    var quantity;
    var quantityInput = jQuery("#ginput_quantity_" + formId + "_" + productFieldId);
    if(quantityInput.length > 0){
        quantity = !gformIsNumber(quantityInput.val()) ? 0 : quantityInput.val();
    }
    else{
        quantityElement = jQuery(".gfield_quantity_" + formId + "_" + productFieldId);

        quantity = 1;
        if(quantityElement.find("input").length > 0)
            quantity = quantityElement.find("input").val();
        else if (quantityElement.find("select").length > 0)
            quantity = quantityElement.find("select").val();

        if(!gformIsNumber(quantity))
            quantity = 0
    }
    quantity = parseFloat(quantity);

    //setting global variable if quantity is more than 0 (a product was selected). Will be used when calculating total
    if(quantity > 0)
        _anyProductSelected = true;

    price = price * quantity;
    price = Math.round(price * 100) / 100;

    return price;
}

function gformGetBasePrice(formId, productFieldId){

    var suffix = "_" + formId + "_" + productFieldId;
    var price = 0;
    var productField = jQuery("#ginput_base_price" + suffix+ ", .gfield_donation" + suffix + " input[type=text]");
    if(productField.length > 0){
        price = productField.val();

        //If field is hidden by conditional logic, don't count it for the total
        if(gformIsHidden(productField)){
            price = 0;
        }
        else if(productField.parents(".gfield_donation" + suffix).length > 0){
            //Formatting open text donation field
            var currency = new Currency(window['gf_currency_config']);
            productField.val(currency.toMoney(price));
        }
    }
    else
    {
        productField = jQuery(".gfield_product" + suffix + " select, .gfield_product" + suffix + " input:checked, .gfield_donation" + suffix + " select, .gfield_donation" + suffix + " input:checked");
        var val = productField.val();
        if(val){
            val = val.split("|");
            price = val.length > 1 ? val[1] : 0;
        }

        //If field is hidden by conditional logic, don't count it for the total
        if(gformIsHidden(productField))
            price = 0;

    }

    var c = new Currency();
    price = c.toNumber(price);
    return price === false ? 0 : price;
}

function gformFormatMoney(text){
    if(!window['gf_currency_config'])
        return text;

    var currency = new Currency(window['gf_currency_config']);
    return currency.toMoney(text);
}

function gformToNumber(text){
    var currency = new Currency();
    return currency.toNumber(text);
}

function gformGetPriceDifference(currentPrice, newPrice){

    //getting price difference
    var diff = parseFloat(newPrice) - parseFloat(currentPrice);
    price = gformFormatMoney(diff);
    if(diff > 0)
        price = "+" + price;

    return price;
}

function gformGetOptionLabel(element, selected_value, current_price){
    element = jQuery(element);
    var price = gformGetPrice(selected_value);
    var current_diff = element.attr('price');
    var label = element.html().replace(/<span(.*)<\/span>/i, "").replace(current_diff, "");

    var diff = gformGetPriceDifference(current_price, price);
    diff = gformToNumber(diff) == 0 ? "" : " " + diff;
    element.attr('price', diff);

    //don't add <span> for drop down items (not supported)
    var label = element[0].tagName.toLowerCase() == "option" ? label + " " + diff : label + "<span class='ginput_price'>" + diff + "</span>";
    return label;
}


function gformGetProductIds(parent_class, element){
    var classes = jQuery(element).hasClass(parent_class) ? jQuery(element).attr("class").split(" ") : jQuery(element).parents("." + parent_class).attr("class").split(" ");
    for(var i=0; i<classes.length; i++){
        if(classes[i].substr(0, parent_class.length) == parent_class && classes[i] != parent_class)
            return {formId: classes[i].split("_")[2], productFieldId: classes[i].split("_")[3]};
    }
    return {formId:0, fieldId:0};
}

function gformGetPrice(text){
    var val = text.split("|");
    var currency = new Currency(window['gf_currency_config']);

    if(val.length > 1 && currency.toNumber(val[1]) !== false)
         return currency.toNumber(val[1]);

    return 0;
}

function gformIsNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function gformRegisterPriceField(item){

    if(!_gformPriceFields[item.formId])
        _gformPriceFields[item.formId] = new Array();

    //ignore price fields that have already been registered
    for(var i=0; i<_gformPriceFields[item.formId].length; i++)
        if(_gformPriceFields[item.formId][i] == item.productFieldId)
            return;

    //registering new price field
    _gformPriceFields[item.formId].push(item.productFieldId);
}

function gformInitPriceFields(){

    jQuery(".gfield_price").each(function(){

        var productIds = gformGetProductIds("gfield_price", this);
        gformRegisterPriceField(productIds);

       jQuery(this).find("input[type=text], select").change(function(){
           var productIds = gformGetProductIds("gfield_price", this);
           if(productIds.formId == 0)
                productIds = gformGetProductIds("gfield_shipping", this);
           gformCalculateTotalPrice(productIds.formId);
       });

       jQuery(this).find("input[type=radio], input[type=checkbox]").click(function(){
           var productIds = gformGetProductIds("gfield_price", this);
           if(productIds.formId == 0)
                productIds = gformGetProductIds("gfield_shipping", this);
           gformCalculateTotalPrice(productIds.formId);
       });
    });

    for(formId in _gformPriceFields)
        gformCalculateTotalPrice(formId);

}

jQuery(document).ready(function(){
    gformInitPriceFields();
});

