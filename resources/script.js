const homeUrl = '/home';

function goHome() {
	window.location.href = homeUrl;
}

window.addEventListener('load', () => {
	document.getElementsByClassName('title')[0].addEventListener('click', goHome);
	document.getElementsByClassName('title')[0].classList.add('hand-mouse-cursor');
	document.getElementsByClassName('logo')[0].addEventListener('click', goHome);
	document.getElementsByClassName('logo')[0].classList.add('hand-mouse-cursor');
	
	/* SORTING COLUMNS */
	// https://stackoverflow.com/a/49041392/2893496
	const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
	const generateComparator = (idx, asc) => (a, b) => ((v1, v2) => 
		v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
		)(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
	
	// https://stackoverflow.com/a/70019926/2893496
	document.querySelectorAll('th[scope="col"]').forEach(th_elem => {
		let asc=true;
		const index = Array.from(th_elem.parentNode.children).indexOf(th_elem);
		th_elem.addEventListener('click', (e) => {
			const arr = [... th_elem.closest("table").querySelectorAll('tbody tr')];
			arr.sort(generateComparator(index, asc));
			arr.forEach(elem => {                   
				th_elem.closest("table").querySelector("tbody").appendChild(elem);
			});
			document.querySelectorAll('th[scope="col"]').forEach(th_old => {
				th_old.classList.remove('sorted-column-asc');
				th_old.classList.remove('sorted-column-desc');
			});
			th_elem.classList.add(asc ? 'sorted-column-asc' : 'sorted-column-desc');
			asc = !asc;
		});
		th_elem.classList.add('sortable-column');
	});
	
	document.querySelectorAll('div.address').forEach(addr_elem => {
		bInc = document.createElement('button');
		bDec = document.createElement('button');
		bInc.appendChild(document.createTextNode('+'));
		bDec.appendChild(document.createTextNode('='));
		
		div = document.createElement('div');
		div.classList.add('resizer');
		div.appendChild(bInc);
		div.appendChild(bDec);
		addr_elem.insertBefore(div, addr_elem.firstChild);
		
		bInc.addEventListener('click', (e) => {
			let curSize = parseInt(addr_elem.style.fontSize);
			if (isNaN(curSize))
			{
				curSize = 100;
			}
			curSize += 10;
			addr_elem.style.fontSize = curSize + '%';
		});
		bDec.addEventListener('click', (e) => {
			addr_elem.style.fontSize = '100%';
		});
	});
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