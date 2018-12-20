
console.log(window.location.href);


document.addEventListener("DOMContentLoaded", function() {

			if(window.location.href=="https://okomo.com/sandbox/"){
				showIframe();
	
				var widgetWrap = document.getElementsByClassName('widget-box')[0];
				var iFrame = document.getElementById('okomoIframeContainer'); 
	
				widgetWrap.appendChild(iFrame);
				
				
				var about = document.getElementById('okomoIframeContainer');
 				console.log(about);
//				about.style.position = 'relative !important';
				about.setAttribute('style', 'position: relative !important');
				
				
			}
	
});

document.addEventListener("DOMContentLoaded", function() {

			if(window.location.href=="https://okomo.com/sandbox/freedemo/"){
				showIframe();
	
				var widgetWrap = document.getElementsByClassName('widget_box_demo')[0];
				var iFrame = document.getElementById('okomoIframeContainer'); 
	
				widgetWrap.appendChild(iFrame);
				
				
				var about = document.getElementById('okomoIframeContainer');
 				console.log(about);
//				about.style.position = 'relative !important';
				about.setAttribute('style', 'position: absolute !important' );
				
				
			}
	
});








//$('.okomo-button').appendTo($('.widget-box'));

//$('#okomoIframeContainer').appendTo($('.widget_box_demo'));


