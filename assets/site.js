// viktor-andrienko.com: theme switch, photo viewer, project gallery stage, filmography filter
(function () {
	'use strict';

	// theme switch (the initial theme is set by an inline script in <head> to avoid a flash)
	function setTheme(t) {
		document.documentElement.setAttribute('data-theme', t);
		try { localStorage.setItem('theme', t); } catch (e) {}
		document.querySelectorAll('.themes button').forEach(function (b) {
			b.setAttribute('aria-pressed', b.getAttribute('data-theme') === t ? 'true' : 'false');
		});
	}
	document.querySelectorAll('.themes button').forEach(function (b) {
		b.addEventListener('click', function () { setTheme(b.getAttribute('data-theme')); });
	});
	setTheme(document.documentElement.getAttribute('data-theme') || 'dark');

	// photo viewer: every a.js-view with the same data-group forms one slideshow
	var viewer = document.createElement('div');
	viewer.className = 'viewer';
	viewer.setAttribute('role', 'dialog');
	viewer.setAttribute('aria-modal', 'true');
	viewer.setAttribute('aria-label', 'Перегляд фото');
	viewer.innerHTML = '<img alt=""><div class="viewer-caption"></div><div class="viewer-bar">' +
		'<button type="button" data-a="prev" aria-label="Попереднє фото">←</button><span class="viewer-n"></span>' +
		'<button type="button" data-a="next" aria-label="Наступне фото">→</button><button type="button" data-a="close">Закрити</button></div>';
	document.body.appendChild(viewer);
	var vImg = viewer.querySelector('img'), vCap = viewer.querySelector('.viewer-caption'), vN = viewer.querySelector('.viewer-n');
	var items = [], idx = 0, opener = null;

	function show(i) {
		idx = (i + items.length) % items.length;
		var a = items[idx];
		vImg.src = a.getAttribute('href');
		vImg.alt = a.getAttribute('data-caption') || '';
		vCap.textContent = a.getAttribute('data-caption') || '';
		vN.textContent = items.length > 1 ? (idx + 1) + ' / ' + items.length : '';
		viewer.querySelectorAll('[data-a="prev"],[data-a="next"]').forEach(function (b) { b.hidden = items.length < 2; });
		if (a.id) history.replaceState(null, '', '#' + a.id);
	}
	function open(a) {
		var g = a.getAttribute('data-group');
		items = Array.prototype.slice.call(document.querySelectorAll('a.js-view' + (g ? '[data-group="' + g + '"]' : '')));
		opener = a;
		viewer.classList.add('open');
		show(items.indexOf(a));
		viewer.querySelector('[data-a="close"]').focus();
	}
	function close() {
		viewer.classList.remove('open');
		vImg.removeAttribute('src');
		history.replaceState(null, '', location.pathname + location.search);
		if (opener) opener.focus();
	}
	document.addEventListener('click', function (e) {
		var a = e.target.closest ? e.target.closest('a.js-view') : null;
		if (a) { e.preventDefault(); open(a); }
	});
	viewer.addEventListener('click', function (e) {
		var act = e.target.getAttribute && e.target.getAttribute('data-a');
		if (act === 'prev') show(idx - 1);
		else if (act === 'next') show(idx + 1);
		else if (act === 'close' || e.target === viewer) close();
	});
	document.addEventListener('keydown', function (e) {
		if (!viewer.classList.contains('open')) return;
		if (e.key === 'Escape') close();
		else if (e.key === 'ArrowLeft') show(idx - 1);
		else if (e.key === 'ArrowRight') show(idx + 1);
	});
	// old photo pages redirect here with #p<id>: open that photo
	if (location.hash.length > 1) {
		var target = document.getElementById(location.hash.slice(1));
		if (target && target.classList.contains('js-view')) open(target);
	}

	// project gallery stage: thumbnails switch the big picture, the big picture opens the viewer
	var stage = document.querySelector('.stage');
	if (stage) {
		var thumbs = Array.prototype.slice.call(document.querySelectorAll('.thumbs a'));
		var sImg = stage.querySelector('.stage-img img'), sCap = stage.querySelector('.stage-cap'), sN = stage.querySelector('.stage-n');
		var cur = 0;
		var pick = function (i) {
			cur = (i + thumbs.length) % thumbs.length;
			var t = thumbs[cur];
			sImg.src = t.getAttribute('href');
			sImg.alt = t.getAttribute('data-caption') || '';
			sCap.textContent = t.getAttribute('data-caption') || '';
			sN.textContent = (cur + 1) + ' / ' + thumbs.length;
			thumbs.forEach(function (x, k) { if (k === cur) x.setAttribute('aria-current', 'true'); else x.removeAttribute('aria-current'); });
		};
		thumbs.forEach(function (t, k) {
			t.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); pick(k); });
		});
		stage.querySelector('[data-s="prev"]').addEventListener('click', function () { pick(cur - 1); });
		stage.querySelector('[data-s="next"]').addEventListener('click', function () { pick(cur + 1); });
		stage.querySelector('.stage-img').addEventListener('click', function () {
			items = thumbs; opener = thumbs[cur]; viewer.classList.add('open'); show(cur);
		});
	}

	// filmography: decade filter
	var filters = document.querySelectorAll('[data-decade]');
	if (filters.length) {
		var rows = document.querySelectorAll('.film-table tbody tr');
		filters.forEach(function (f) {
			f.addEventListener('click', function () {
				var d = f.getAttribute('data-decade');
				filters.forEach(function (x) { x.setAttribute('aria-pressed', x === f ? 'true' : 'false'); });
				rows.forEach(function (r) { r.hidden = d !== 'all' && r.getAttribute('data-decade') !== d; });
			});
		});
	}
})();
