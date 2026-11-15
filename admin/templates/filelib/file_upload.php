<html>
    <head>
        <meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?= ADMIN_CHARSET ?>">
        <meta http-equiv="Expires" content="0">
        <meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
        <meta http-equiv="pragma" content="no-cache">
        <link rel=stylesheet href=/admin/css.css type="text/css">
              <title><?= $AdminTrnsl["DownloadLibUploadfile"] ?></title>

        <script type="text/javascript" src="/admin/js/html5upload.js"></script>
        <style type="text/css">
            #custom-queue { border: 1px solid #E5E5E5; min-height: 60px; max-height: 300px; overflow: auto; margin: 10px 0; width: 370px; }
            .h5u-item { font: 11px Verdana, Geneva, sans-serif; padding: 6px 10px; border-bottom: 1px solid #E5E5E5; }
            .h5u-bar { background: #E5E5E5; height: 3px; margin-top: 4px; }
            .h5u-fill { background: #0099FF; height: 3px; width: 0; }
            .h5u-done { color: #999; }
            .h5u-error { background: #FDE5DD; }
        </style>
    </head>

    <body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

        <table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
        <table width=100% border=0 cellpadding=4 cellspacing=0>
            <tr class=top41>
                <td height=30 id=pad18 colspan=2 nowrap class=head><b><?= $AdminTrnsl["DownloadLib"] ?> : <?= $AdminTrnsl["DownloadLibUploadingFile"] ?></b></td>
            </tr>
        </table>
        <table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
        <table width=100% border=0 cellpadding=4 cellspacing=0>
            <tr>
                <td colspan=2 id=pad18 class=h4>

                    <br>
                    <table width=100% border=0 cellpadding=0 cellspacing=0><tr><td>
                                <?php  if (strlen($cdirPath) != 0) { ?>
                                    <b class=h3><a href="/admin/filelib.php?cname=<?= $cname ?>"><?= $AdminTrnsl["DownloadLibMainFolder"] ?></a></b><?php  foreach (explode("/", $cdirPath) as $d) { if (!$d) { continue; } ?><?php  $cd = $cd . "/" . $d ?><b> : <a href="/admin/filelib.php?cdir=<?= $cd ?>&cname=<?= $cname ?>"><?= $d ?></a></b><?php  } ?>
                                <?php  } else { ?>
                                    <b><a href="/admin/filelib.php?cname=<?= htmlspecialchars((string)($cname ?? "")) ?>"><?= $AdminTrnsl["DownloadLibMainFolder"] ?></a></b>
                                <?php  } ?>
                            </td></table>
                    <br>
                    <script>
                        function checkFName(fname) {
                            var i = fname.lastIndexOf("/");
                            if (i == -1) {
                                i = fname.lastIndexOf("\\");
                            }
                            fname = fname.substring(i+1);

                            if (!fname.match(/^[A-Za-z0-9_\-\. ]+$/)) {
                                return false;
                            }
                            return true;
                        }</script>




                    <div class="demo-box">
                        <input id="custom_file_upload" type="file" multiple />
                        <div id="errors"></div>
                        <div id="custom-queue"></div>
                    </div>
                    <script type="text/javascript">
                        var uploadedFiles = [];
                        html5Upload({
                            input: document.getElementById('custom_file_upload'),
                            queue: document.getElementById('custom-queue'),
                            url: '/admin/js/uploadify/uploadify_files.php',
                            maxFiles: 20,
                            concurrency: 3,
                            accept: function (file) {
                                return checkFName(file.name) ? '' : <?= json_encode((string)($AdminTrnsl["Upload_BadFilename"] ?? '')) ?>;
                            },
                            onFile: function (file, response) {
                                uploadedFiles.push(response);
                            },
                            onDone: function () {
                                document.getElementById('saveUploadedFiles').style.display = uploadedFiles.length ? '' : 'none';
                            }
                        });
                        function submitUploadedFiles() {
                            document.querySelector('#fileForm input[name=files]').value = uploadedFiles.join('|#|');
                            document.getElementById('fileForm').submit();
                        }
                    </script>

                    <input id="saveUploadedFiles" style="margin-top:20px; display:none;" type="button" class="button5" value="<?=$AdminTrnsl["Save"]?>" onclick="submitUploadedFiles()">
                </td>
            </tr>
        </table>

        <form id="fileForm" action="/admin/filelib.php" method=POST enctype="multipart/form-data">
            <input type="hidden" name="do" value="file_upload_uploadify" />
            <input type="hidden" name="cname" value="<?= $cname ?>" />
            <input type="hidden" name="cdir" value="<?= $cdirPath ?>" />
            <input type="hidden" name="files" value="" />
        </form>
    </body>
</html>
