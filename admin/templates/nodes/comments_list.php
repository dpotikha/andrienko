<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/admin/prepend.php");


	
$superadmin = $CurrentAdminUser->group->id == 1 ? 1 : 0;
$node_id = (int) $request->getParameter("node_id");
if ($request->getParameter("view") == 'list') {
$paginator = new CPaginatorByQuery("SELECT count(id) as count FROM dbm_comments WHERE node_id = $node_id",
        "SELECT * FROM dbm_comments WHERE node_id = $node_id ORDER BY date ASC LIMIT {#offset#}, {#limit#}", ADMIN_COMMENTS_PER_PAGE, 10000);
} else {
    $paginator = new CPaginatorByArray(buildComments($node_id), ADMIN_COMMENTS_PER_PAGE, 10000);
    if ($paginator->values) {
        foreach ($paginator->values as $k => $v)
            $commentIds[] = $v['id'];
        $MDB->Query("SELECT * FROM dbm_comments WHERE id = " . implode(' OR id = ', $commentIds));
        $c = $MDB->getRecords();
        foreach ($c as $k => $v)
            $t[$v['id']] = $v;
        foreach ($paginator->values as $k => $v) {
            $paginator->values[$k] = array_merge($paginator->values[$k], $t[$v['id']]);
            $paginator->values[$k]['level'] = min($paginator->values[$k]['level'], MAX_COMMENTS_LEVEL);
        }
    }
}

?>
<input id="currentPage" value="<?= $paginator->currentPage ?>" type="hidden" />
<?php  if ($paginator->values) { ?>
    <div class="active1 commentWrap commentlevel0"><div class="commentsListRight"><input type="checkbox" id="allCheckbox" value="all" /></div><div class="clear"></div></div>
    <?php  foreach ($paginator->values as $k => $v) { ?>
        <div class="active<?= $v['active'] ?> commentWrap commentlevel<?= (int)$v['level'] ?>" id="commentWrap<?= $v['id'] ?> ">
            <div class="commentsListLeft">
                <?php  if ($v['user']) { ?><a style="color: #0272BA;" href="/admin/nodes.php?do=editnode&id=<?= $v['user'] ?>"><?php  } ?>[ <?= $v['name'] ?> ]<?php  if ($v['user']) { ?></a><?php  } ?>&nbsp;&nbsp;&nbsp;<?= date('d.m.Y H:i', strtotime($v['date'])) ?>
            </div>
            <div class="commentsListRight">
                <?php  if ($superadmin) { ?>&nbsp;&nbsp;&nbsp;<span><?= $v['ip'] ?>&nbsp;&nbsp;&nbsp;<?= $v['session_id'] ?>&nbsp;&nbsp;&nbsp;</span><?php  } ?>
                <a href="javascript:;" rel="<?= $v['id'] ?>" class="addComment"><img border="0" id="hint" src="/admin/_img/ico/plus.png" tooltip="<?=$AdminTrnsl["Generic_Reply"]?>"></a>
                <a href="javascript:;" rel="<?= $v['id'] ?>" class="editComment"><img border="0" id="hint" src="/admin/_img/ico/edit.jpg" tooltip="<?=$AdminTrnsl["Generic_Edit_Full"]?>"></a>
                <a href="javascript:;" rel="<?= $v['id'] ?>" class="removeComment"><img border="0" id="hint" src="/admin/_img/ico/delete.jpg" tooltip="<?=$AdminTrnsl["Delete"]?>"></a>
                <?php  if ($v['active']) { ?>
                    <a href="javascript:;" rel="<?= $v['id'] ?>" class="blockComment"><img border="0" id="hint" src="/admin/_img/block.gif" tooltip="<?=$AdminTrnsl["Generic_Block"]?>"></a>
                <?php  } else {?>
                    <a href="javascript:;" rel="<?= $v['id'] ?>" class="unblockComment"><img border="0" id="hint" src="/admin/_img/eye.png" tooltip="<?=$AdminTrnsl["Generic_Unblock"]?>"></a>
                <?php  } ?>
                <input type="checkbox" class="commentCheckbox" value="<?= $v['id'] ?>" />
            </div>
        <div class="clear"></div>
        <div class="commentsText" id="commentsText<?= $v['id'] ?>"><?= nl2br($v['text']) ?></div>
        <div style="display:none;" class="_commentsText commentsEditText" id="commentsTextAreaDiv<?= $v['id'] ?>">
            <textarea class="textInput" id="commentsTextArea<?= $v['id'] ?>"><?= $v['text'] ?></textarea>
            <textarea class="textBackup" id="commentsBackupArea<?= $v['id'] ?>"><?= $v['text'] ?></textarea>
            <a href="javascript:;" class="saveComment" rel="<?= $v['id'] ?>"><img border="0" src="/admin/_img/ico/save.png" tooltip="<?=$AdminTrnsl["Save"]?>"></a>
            <a href="javascript:;" class="cancelSaveComment" rel="<?= $v['id'] ?>"><img border="0" src="/admin/_img/ico/stop.png" tooltip="<?=$AdminTrnsl["Generic_Cancel"]?>"></a>
        </div>
        <?= showAddBlock($v['id']) ?>
        </div>

    <?php  } ?>
    <div id="bigButtons">
        <input type="button" value="<?=$AdminTrnsl["Generic_Block"]?>" class="button1" id="blockSelected">
        <input type="button" value="<?=$AdminTrnsl["Generic_Unblock"]?>" class="button1" id="unblockSelected">
        <input type="button" value="<?=$AdminTrnsl["Delete"]?>" class="button3" id="removeSelected">
    </div>
    <div class="clear" />
<?php  } else { ?>
    <div style="padding:15px;"><h4><?=$AdminTrnsl["Comments_None"]?></h4></div>
<?php  } ?>
<?php  if ($paginator->totalPages > 1) { ?>
    <div class="listingArea">
        <?php  for ($p = $paginator->startPage; $p <= $paginator->endPage; $p++) { ?>
            <?php  if ($p == $paginator->currentPage) { ?>
                <span><?= 1 + ($p - 1) * ADMIN_COMMENTS_PER_PAGE . '..' . min($p * ADMIN_COMMENTS_PER_PAGE, $paginator->totalRecords) ?></span>&nbsp;
            <?php  } else { ?>
                <a href="javascript:;" rel="<?= $p ?>" class="page"><?= 1 + ($p - 1) * ADMIN_COMMENTS_PER_PAGE . '..' . min($p * ADMIN_COMMENTS_PER_PAGE, $paginator->totalRecords) ?></a>&nbsp;
            <?php  } ?>
        <?php  } ?>
    </div>
<?php  } ?>

