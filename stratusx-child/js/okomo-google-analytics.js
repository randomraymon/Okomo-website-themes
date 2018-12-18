

document.addEventListener("DOMContentLoaded", function() {

    //
    // Google Analytics Events
    //

    // Website

    var websiteTriggers = [
        {
            elId: false,
            elClass: 'frm_submit',
            elIndex: 0,
            evCategory: 'Home Page Clicks',
            evLabel: 'Free Demo Button'
        }
    ]

    websiteTriggers.forEach(trigger => {
        addGaEvent(trigger.elId, trigger.elClass, trigger.elIndex, trigger.evCategory, trigger.evLabel);
    });

    // Widget

    var widgetTriggers = [
        {
            elId: false,
            elClass: 'okomo-button',
            elIndex: 0,
            evCategory: 'Widget Clicks',
            evLabel: 'Open Widget'
        }
    ]

    widgetTriggers.forEach(trigger => {
        addGaEvent(trigger.elId, trigger.elClass, trigger.elIndex, trigger.evCategory, trigger.evLabel);
    });

    // function declaration
    
    function addGaEvent (elId, elClass, elIndex, evCategory, evLabel) {

        var domElement;

        if(document.getElementById(elId)) {
            domElement = document.getElementById(elId);
        } else if (domElement = document.getElementsByClassName(elClass)[elIndex]) {
            domElement = document.getElementsByClassName(elClass)[elIndex];
        } else {
            return;
        }

        domElement.addEventListener('click', function() {
            gtag('event', 'click', {'event_category' : evCategory, 'event_label' : evLabel});
            console.log('Event sent!');
        });
            
    }


});