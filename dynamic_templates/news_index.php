<? tinclude("_head") ?>
<?= $CurrentNode->tfields["text"]?>
<?
$news = Node::findByQuery("SELECT n.* FROM {news_item} n" .
	" ORDER BY n.date DESC");
$newsCount = Node::findCountByQuery("SELECT COUNT(*) FROM {news_item} n");
	
$rpp = 10;
$lpp = 10;
$pageNum = (int)$request->getParameter("page");
$offset = $pageNum * $rpp;

?>
<? for($i=$offset; $i < ($offset + $rpp) && $i < $newsCount; $i++) { 
	$n =& $news[$i];
	$img = $n->tfields["photo"];
?>
<div class="newsMain" id="bgNo">
	<h6 style="padding-left:0px"><?= getFormattedDate($n->tfields["date"]["formatted_date"]) ?></h6>
	<a href="<?= $n->getHref() ?>" class="newsName">
	<?= $n->name ?>
	</a>
	<table cellpadding="0" cellspacing="0" border="0" class="newsContent" id="bgNo" width="453">
		<tr>
			<td>
			<? if ($img["url"]) { ?>
				<table cellpadding="0" cellspacing="0" border="0" id="shadowTab">
					<tr>
						<td><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+18 ?>" alt="" class="shadowImgTL"></td>
						<td><div class="shadowImgDetailTL_01">
								<div class="shadowImgDetailBR_01" id="shadowNews"> <a href="<?= $n->getHref() ?>"><img src="<?= $img["thumburl"] ?>" width="<?= $img["thumbwidth"] ?>" height="<?= $img["thumbheight"] ?>" alt=""></a></div>
							</div></td>
					</tr>
					<tr>
						<td colspan="2" align="right"><img src="/_img/s.gif" width="90" height="5" alt=""></td>
					</tr>
				</table>
				<? } ?>
			</td>
			<td  class="textNews"><?= $n->tfields["announce"] ?>
				<div class="newsLinkDetail"><a href="<?= $n->getHref() ?>">Читать подробнее</a><img src="/_img/arrow_02.gif" width="18" height="7" alt=""></div></td>
		</tr>
	</table>
</div>
<? } ?>
<? if ($newsCount > $rpp) { ?>
<div class="pager"><table class="pager">
<? HTMLWriter::draw_paging_links($offset, $rpp, $lpp, $newsCount, array(), array(), "_draw_paging_link");?> 
</table></div>
<? } ?>
<? tinclude("_foot") ?>