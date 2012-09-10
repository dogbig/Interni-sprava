// Fancy getter for cust. selector
function GetValueFromCustomerSelector(selname,selIDs)
{
    document.getElementById('cmp_id').value = selIDs;
    document.getElementById('cmp').value = selname;
}

// Fancy getter for user selector
function GetValueFromUserSelector(selname,selIDs)
{
    document.getElementById('fwto_id').value = selIDs;
    document.getElementById('fwto').value = selname;
}

// Fancy dialogs (addEdit etc) using .latte files
function showUrlInDialog(url,titletorender,w,h){
    var tag = $("<div></div>");
    $.ajax({
        url: url,        
        success: function(data) { 
            var defaultDate;
            var dialog = tag.html(data).dialog({
                modal: true,
                width : w,
                position: 'center' ,
                title: titletorender,
                resizable : false,
                height : h,
                open: function() { 
                    defaultDate = $('.date').val();
                    defaultCmpId = $('#cmp_id').val();
                },
                beforeclose: function() {
                    // parser of data format from datepicker
                    if (somethingChanged == true |
                        defaultCmpId!=$('#cmp_id').val() | 
                        Date.parse(Date.parseString(defaultDate))
                        !==Date.parse(Date.parseString($('.date')
                            .val(),"dd.MM.yyyy"))
                        & $('.date').val()!=="" & $('.date').length > 0) 
                        {
                        if (!confirm(
                            "Provedené zm\u011bny budou ztraceny, pokračovat?"))
                            { 
                            return false;
                        }                         
                    }                     
                },
                close: function() {                    
                    window.location.reload();
                }     
            }).dialog('open');             
            
            
            // Nette Forms Validator
            for (var i = 0; i < document.forms.length; i++) {
                Nette.initForm(document.forms[i]);
            }
            
            // Detect changes
            var somethingChanged = false;
            dialog.change(function() { 
                somethingChanged = true; 
            })
            
            // Close using Close button
            $('#btn-close').click(function() {  
                dialog.dialog("close");                
            });
        }
    });   
    
}  

// Google Maps API V3
function loadMapWithAdress() {
    // Adress from element <div>
    var address = $("#adresa").text();
    // alert("Hledana adresa: " + address);
    // Geocoder API
    geocoder = new google.maps.Geocoder();
    geocoder.geocode( {
        'address': address
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var myOptions = {
                zoom: 10,
                panControl: true,
                zoomControl: true,
                scaleControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById(
                "mapContainer"),myOptions);
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map, 
                position: results[0].geometry.location                
            });
        } else {
            $("#mapContainer").html($("<img>")
                .attr("src", "../../images/notMap.png"));            
        }
    });
}
// dialog for flash messages
function showDialog(){
    $( "#logonFlashesh" ).dialog({
        height: 140,
        width : 250,
        title: "Informace",
        resizable : false,
        modal: true
    });
    
    $('#dialogCloser1').click(function () {     
        $("#logonFlashesh").dialog("close");
        return false;
    });
    
}

// Document onload
window.onload = function()
{
    // dialog for flash messages
    if ($('#logonFlashesh').length > 0) {
        showDialog();
    }

    // ready for Google Maps
    if ($('#mapContainer').length > 0) {
        loadMapWithAdress();
    } 

    // Warn. when not using jQuery
    if(!window.jQuery)
    {
        alert('jQuery neni nacteno!');
    } else {
        $('.datepicker').datepicker();
        $(document).ready(function(){
            $('.toolTipUp').tooltip({
                'placement':'top', 
                'trigger' : 'hover'
            });
            $('.toolTipDown').tooltip({
                'placement':'bottom', 
                'trigger' : 'hover'
            }); 
        }); 
    }    
    
}

$(document).ready(function(){
    $("#spinner").bind("ajaxSend", function() {
        // TODO var $overlay = $('<div class="ui-overlay"><div class="ui-widget-overlay"></div></div>').hide().appendTo('body');
        // TODO $overlay.show()
        $(this).show();        
    }).bind("ajaxStop", function() {
        $(this).hide();

    }).bind("ajaxError", function() {
        $(this).hide();

    });

});







    
