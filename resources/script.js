const homeUrl = '/home';

function goHome() {
	window.location.href = homeUrl;
}

window.addEventListener('load', () => {
	document.getElementsByClassName('title')[0].addEventListener('click', goHome);
	document.getElementsByClassName('title')[0].classList.add('hand-mouse-cursor');
	document.getElementsByClassName('logo')[0].addEventListener('click', goHome);
	document.getElementsByClassName('logo')[0].classList.add('hand-mouse-cursor');
});


// https://stackoverflow.com/questions/60595630/javascript-use-input-type-file-to-compute-sha256-file-hash
function hashfile(form, fileselector, func)
{
	return function() {
		readbinaryfile(fileselector.files[0])
			.then(function(result) {
				result = new Uint8Array(result);
				return window.crypto.subtle.digest('SHA-256', result);
			}).then(function(result) {
				result = new Uint8Array(result);
				const resulthex = Uint8ArrayToHexString(result);
				func(resulthex);
			});
	};
}

function readbinaryfile(file)
{
	return new Promise((resolve, reject) => {
		const fr = new FileReader();
		fr.onload = () => {
			resolve(fr.result)
		};
		fr.readAsArrayBuffer(file);
	});
}

function Uint8ArrayToHexString(ui8array)
{
	var hexstring = '',
		h;
	for (var i = 0; i < ui8array.length; i++) {
		h = ui8array[i].toString(16);
		if (h.length == 1) {
			h = '0' + h;
		}
		hexstring += h;
	}
	const p = Math.pow(2, Math.ceil(Math.log2(hexstring.length)));
	hexstring = hexstring.padStart(p, '0');
	return hexstring;
}

// https://stackoverflow.com/questions/12460378/how-to-get-json-from-url-in-javascript#12460434
function getJson(url, callback) // callback in format function(err, data)
{
	var xhr = new XMLHttpRequest();
	xhr.open('GET', url, true);
	xhr.responseType = 'json';
	xhr.onload = function() {
		var status = xhr.status;
		if (status === 200) {
			callback(null, xhr.response);
		} else {
			callback(status, xhr.response);
		}
	};
	xhr.send();
};

function getJsonResponse(url, data, callback)
{
	formData = new FormData();
	for(const key in data)
	{
		formData.append(key, data[key]);
	}
	var xhr = new XMLHttpRequest();
	xhr.open('POST', url, true);
	xhr.responseType = 'json';
	xhr.onload = function() {
		var status = xhr.status;
		if (status === 200) {
			callback(null, xhr.response);
		} else {
			callback(status, xhr.response);
		}
	};
	xhr.send(formData);
}


// // //
function linkHash(form, hash)
{
	const type = form['type'].value;
	const what = form['what'].value;
	// using onsubmit rather than addEventListener to prevent previous ones from firing
	form.onsubmit = (event) => {
		event.preventDefault();
		console.log("Linking "+hash);
		const data = {
			'type': type,
			'what': what,
			'hash': hash
		};
		getJsonResponse('/api_linkimage', data, (err, json) => {
			if(err == null && json['image_link_status'] == 'ok')
			{
				window.location.reload();
			}
		});
	};
}