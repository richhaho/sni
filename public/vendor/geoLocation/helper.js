
let cordovaHelper = 
{
	initialized: false,
	callback: null,

	init: function()
	{
		window.addEventListener("message", (event) =>
		{
			if (event.data.type == 'currentPosition')
			{
				if (this.callback)
					this.callback (event.data);
			}
		}, false);

		this.initialized = true;
	},

	isCordova: function()
	{
		if (!this.initialized)
			this.init();

		return window.parent !== window.self;
	},

	getCurrentPosition: function (callback)
	{
		this.callback = callback;
		window.parent.postMessage({ type: 'getCurrentPosition' }, '*');
	}
};


/*
**	Attempts to get GPS location, and executes the callback with a single `data` parameter,
**
**	This parameter `data` is an object contaning:
**		error: string, latitude:float, longitude:float, accuracy:float
**
**	If error is `null` it means the call was successful.
*/
function getCoordinates (callback)
{
	if (cordovaHelper.isCordova())
	{
		cordovaHelper.getCurrentPosition(function(data)
		{
			callback (data);
		});
	}
	else
	{
		// Use your regular navigator.geolocation stuff, and pass the same parameters (error, latitude, longitude, accuracy) to the callback.
		navigator.geolocation.getCurrentPosition(function(data) {
			callback (data);
		});
	}
}
