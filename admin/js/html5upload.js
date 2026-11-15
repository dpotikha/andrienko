/*
 * Minimal HTML5 multi-file uploader for the admin (replaces the Flash-based uploadify widget).
 * Posts each file as "Filedata" (same field name uploadify used) to the existing PHP endpoints;
 * the admin session cookie authenticates the request.
 *
 * html5Upload({
 *   input:       <input type="file" multiple> element,
 *   queue:       element that lists the files with a progress bar,
 *   url:         endpoint URL,
 *   data:        {name: value} extra POST fields,
 *   maxFiles:    max files per selection,
 *   concurrency: parallel uploads,
 *   accept:      function(file) -> error text or '' (optional),
 *   onFile:      function(file, responseText) (optional, per successful file),
 *   onDone:      function(okCount, errorCount) (optional, after the whole batch)
 * });
 */
function html5Upload(opt) {
    var concurrency = opt.concurrency || 3;

    opt.input.addEventListener('change', function () {
        var files = Array.prototype.slice.call(opt.input.files || []);
        opt.input.value = '';
        if (!files.length) return;
        if (opt.maxFiles && files.length > opt.maxFiles) files = files.slice(0, opt.maxFiles);

        var jobs = [], ok = 0, errors = 0, running = 0, next = 0;

        files.forEach(function (file) {
            var row = document.createElement('div');
            row.className = 'h5u-item';
            var label = document.createElement('div');
            label.textContent = file.name;
            var bar = document.createElement('div');
            bar.className = 'h5u-bar';
            var fill = document.createElement('div');
            fill.className = 'h5u-fill';
            bar.appendChild(fill);
            row.appendChild(label);
            row.appendChild(bar);
            opt.queue.appendChild(row);

            var err = opt.accept ? opt.accept(file) : '';
            if (err) {
                row.className += ' h5u-error';
                label.textContent = file.name + ' - ' + err;
                errors++;
            } else {
                jobs.push({file: file, row: row, label: label, fill: fill});
            }
        });

        function finish() {
            if (running === 0 && next >= jobs.length && opt.onDone) opt.onDone(ok, errors);
        }

        function start() {
            while (running < concurrency && next < jobs.length) send(jobs[next++]);
            finish();
        }

        function send(job) {
            running++;
            var fd = new FormData();
            for (var k in (opt.data || {})) fd.append(k, opt.data[k]);
            fd.append('Filedata', job.file, job.file.name);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', opt.url, true);
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) job.fill.style.width = Math.round(e.loaded / e.total * 100) + '%';
            };
            xhr.onloadend = function () {
                running--;
                if (xhr.status === 200) {
                    ok++;
                    job.fill.style.width = '100%';
                    job.row.className += ' h5u-done';
                    if (opt.onFile) opt.onFile(job.file, xhr.responseText);
                } else {
                    errors++;
                    job.row.className += ' h5u-error';
                    job.label.textContent = job.file.name + ' - ' + (xhr.status ? xhr.status + ' ' + xhr.responseText : 'network error');
                }
                start();
            };
            xhr.send(fd);
        }

        start();
    });
}
