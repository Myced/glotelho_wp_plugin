// var ajaxurl = "https://glotelho.cm/wp-admin/admin-ajax.php";
var ajaxurl = "http://localhost/glotelho/wp-admin/admin-ajax.php";

//variable to hold the actual zone for livraison
var zones = [] //an array of objects that contains all zones
var zone = {}; //initialise it to an empty object.

jQuery(document).ready(function(){

    //when the document loads, make a request and get all the zones.
    //so that we can show its details.
    var selected_zone_id =  jQuery("#zone").children("option:selected").val();

    if(selected_zone_id !== "-01")
    {
        //make an ajax request to get all the zones for this quarter
        fetchAndSaveZones(getSelectedQuarter);
    }

    jQuery('.popup').magnificPopup({
        items: {
          src: '#le-zone',
          type: 'inline'
        }
    });

    //add event listeners to the parameters to make ajax requests
    jQuery("#region").change(function(){


        //get the selected region and get the towns in that region
        var region_id = jQuery(this).val();

        page_loading_on();

        jQuery.ajax({
            url : ajaxurl,
            method : "post",
            data : { region_id : region_id, action: "gtplugin_get_towns" },
            error : function(error)
            {
                console.log(error.responseText);
                alert("Encountered an error");
                page_loading_off();
            },
            success: function(data)
            {
                page_loading_off();

                var towns  = jQuery.parseJSON(data);

                showTowns(towns);

            }
        })
    });

    //any other onload event handlers

});

//start the magnific popup on the small button on the checkout page
jQuery(document).on('click', '.mini_popup', function(){
    //open it direcly from the api
    jQuery.magnificPopup.open({
        items: {
            src: '#le-zone',
            type: 'inline'
        }
    });

});

//attach event listeners for towns and quarters here.
jQuery(document).on("click", "#town", function(){
    selectedTown = getSelectedTown();

    //other code goes here
    fetchQuarters(selectedTown);
});

jQuery(document).on("click", "#quarter", function(){
    selectedQuarter = getSelectedQuarter();

    //other code goes here
    fetchZones(selectedQuarter);
});


//handle an event of selecting the zone
jQuery(document).on("click", "#zone", function(){
    selectedZone = getSelectedZone();

    //other code goes here
    if(selectedZone === false)
    {
        hideShippingDetails();
        clearPreviousZoneValues();
    }
    else {
        displayZoneInfo(selectedZone);
    }
});

//handle close event for the zone
jQuery("#close-btn").click(function(){
    //update the regions, zone and town info.

    //get the selected zone
    var myzone = getSelectedZone();

    if( myzone === false)
    {
        clearPreviousZoneValues();
        alert("Vous devez choisir la zone de livraison");
    }

    //reload the checkout data section
    jQuery(document.body).trigger('update_checkout');

    //close maginific popup
    jQuery.magnificPopup.close();
});


function getSelectedTown()
{
    return jQuery("#town").children("option:selected").val();
}

function getSelectedQuarter()
{
    return jQuery("#quarter").children("option:selected").val();
}

function getSelectedZone()
{
    var zone_id =  jQuery("#zone").children("option:selected").val();

    //go through the array of zones and return the one that matches.
    for(var i = 0; i < zones.length; i++)
    {
        var currentZone = zones[i];

        if(currentZone.ID == zone_id)
        {
            zone = currentZone;
            return currentZone;
        }

    }

    return false;
}


function showTowns(towns)
{
    //now go ahead and populate the towns list
    $townSelect = jQuery("#town");

    //now remove all its children
    $townSelect.children("option").remove();

    if(towns.length === 0)
    {
        var option = "<option value=\"-1\"> Aucune ville trouvée </option>";
        $townSelect.append(option);
    }
    else {

        //append the first select your zone option
        $townSelect.append(getSelectOption("Ville"));

        for(var i = 0; i < towns.length; i++)
        {
            var currentTown = towns[i];

            var option = "<option value=\"" + currentTown.term_id + "\">"
                        + currentTown.name + "</option>";

            //append it to the object.
            $townSelect.append(option);
        }
    }
}

function fetchQuarters(town_id)
{
    page_loading_on();

    //get the town in relation.
    jQuery.ajax({
        url : ajaxurl,
        method : "post",
        data : { town_id : town_id, action: "gtplugin_get_quarters" },
        error : function(error)
        {
            page_loading_off();
            console.log(error.responseText);
        },
        success: function(data)
        {
            page_loading_off();
            var quarters  = jQuery.parseJSON(data);
            showQuarters(quarters);
        }
    })
}

function showQuarters(quarters)
{

    $quartersElemement = jQuery("#quarter");

    //clear all its children
    $quartersElemement.children("option").remove();

    if(quarters.length === 0)
    {
        var option = "<option value=\"-1\"> Aucun quartier trouvé </option>";
        $quartersElemement.append(option);
    }
    else {

        //append the first select your zone option
        $quartersElemement.append(getSelectOption("Quartier"));

        for(var i = 0; i < quarters.length; i++)
        {
            var currentQuarter = quarters[i];

            var option = "<option value=\"" + currentQuarter.term_id + "\">"
                        + currentQuarter.name + "</option>";

            //append it to the object.
            $quartersElemement.append(option);
        }
    }
}

function fetchZones(quarter_id)
{
    page_loading_on();

    //get the town in relation.
    jQuery.ajax({
        url : ajaxurl,
        method : "post",
        data : { quarter_id : quarter_id, action: "gtplugin_get_zones" },
        error : function(error)
        {
            page_loading_off();
            console.log(error.responseText);
        },
        success: function(data)
        {
            page_loading_off();
            var all_zones  = jQuery.parseJSON(data);

            zones = all_zones;

            showZones(zones);
        }
    })
}

function fetchAndSaveZones(quarter_id)
{
    page_loading_on();
    //get the town in relation.
    jQuery.ajax({
        url : ajaxurl,
        method : "post",
        data : { quarter_id : quarter_id, action: "gtplugin_get_zones" },
        error : function(error)
        {
            page_loading_off();
            console.log(error.responseText);
        },
        success: function(data)
        {
            page_loading_off();
            var all_zones  = jQuery.parseJSON(data);

            zones = all_zones;
        }
    })
}

function showZones(zones)
{
    $zonesElemement = jQuery("#zone");

    //clear all its children
    $zonesElemement.children("option").remove();

    if(zones.length === 0)
    {
        var option = "<option value=\"-1\"> Aucune zone trouvée </option>";
        $zonesElemement.append(option);
    }
    else {

        //append the first select your zone option
        $zonesElemement.append(getSelectOption("Zone"));

        for(var i = 0; i < zones.length; i++)
        {
            var currentZone = zones[i];

            var option = "<option value=\"" + currentZone.ID + "\">"
                        + currentZone.post_title + "</option>";

            //append it to the object.
            $zonesElemement.append(option);
        }
    }
}

function clearPreviousZoneValues()
{
    //create an array of elements to make empty
    var elements = [

        "#zone_title", "#cost", "#description"

    ];

    for(var i = 0; i <= elements.length; i++)
    {
        var object = jQuery(elements[i]);

        if(object !== null)
        {
            object.text("");
        }
    }
}

function displayZoneInfo(zone)
{
    //grab the elements we need
    $cost = jQuery("#cost");
    $description = jQuery("#description");

    showZoneDetails();

    jQuery("#zone_title").text(zone.post_title);
    $cost.text(formatMoney(zone.post_author, 0));
    $description.html(zone.post_content);
}

function showZoneDetails()
{
    $zone_id = jQuery("#zone_hide");

    if($zone_id.hasClass("hide"))
        $zone_id.removeClass('hide');
}

//this function returns the string for a select option
//which say select your "option name"
function getSelectOption(name)
{
    var option = "<option value=\"-1\"> Sélectionner votre "
                + name + "</option>";

    return option;
}


function formatMoney(n, c, d, t) {
  var c = isNaN(c = Math.abs(c)) ? 2 : c,
    d = d == undefined ? "." : d,
    t = t == undefined ? "," : t,
    s = n < 0 ? "-" : "",
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
    j = (j = i.length) > 3 ? j % 3 : 0;

  return s + (j ? i.substr(0, j) + t : "")
            + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t)
            + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function page_loading_on()
{
    jQuery('#gt-loading-image').addClass("gt_loading");
}

function page_loading_off(){
    jQuery('#gt-loading-image').removeClass("gt_loading");
}
