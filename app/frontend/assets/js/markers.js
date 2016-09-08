function createMarker(map,point,root,the_link,the_title,color,callout) {

	var blackIcon = root + "/app/frontend/assets/images/map-marker-black.png";
	var blueIcon = root + "/app/frontend/assets/images/map-marker-blue.png";
	var greenIcon = root + "/app/frontend/assets/images/map-marker-green.png";
	var pinkIcon = root + "/app/frontend/assets/images/map-marker-pink.png";
	var purpleIcon = root + "/app/frontend/assets/images/map-marker-purple.png";
	var redIcon = root + "/app/frontend/assets/images/map-marker-red.png";
	var tealIcon = root + "/app/frontend/assets/images/map-marker-teal.png";
	var whiteIcon = root + "/app/frontend/assets/images/map-marker-white.png";
	var yellowIcon = root + "/app/frontend/assets/images/map-marker-yellow.png";

	var customIcon = color;
	
	var image = root + "/app/frontend/assets/images/map-marker-red.png";
	
	if(color == 'blue')			{ image = blueIcon } 
	else if(color == 'red')		{ image = redIcon } 
	else if(color == 'green')	{ image = greenIcon } 
	else if(color == 'yellow')	{ image = yellowIcon } 
	else if(color == 'teal')	{ image = tealIcon } 
	else if(color == 'black')	{ image = blackIcon }  
	else if(color == 'white')	{ image = whiteIcon } 
	else if(color == 'purple')	{ image = purpleIcon } 
	else if(color == 'pink')	{ image = pinkIcon } 
	else { image = customIcon } 
		
	var marker = new google.maps.Marker({
    	map:map,
   		draggable:false,
    	animation: google.maps.Animation.DROP,
    	position: point,
    	icon: image,
    	title: the_title
  	});
  	
  	var infowindow = new google.maps.InfoWindow({
        content: callout
    });
    
  	google.maps.event.addListener(marker, 'click', function() {
  		if ( callout == '' ) {
  			window.location = the_link;
  		} else {
  			infowindow.open(map,marker);
  		} 
  	});
  	
  	return marker;
	
}