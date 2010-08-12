<?php
defined('_JEXEC') or die('Restricted access');
/* JS Script f�r Joomla Sortierung */
JFactory::getDocument()->addScriptDeclaration( "
	function tableOrdering( order, dir, task ) {
		var form = document.adminForm;
		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		form.submit( task );
	}"
);
?>
<div class="ss-speaker-container" >
<h1 class="componentheading"><?php echo $this->title ?></h1>
<!-- Begin Data - Speaker -->
<?php if($this->row->pic) { ?>
	<a href="<?php echo JRoute::_('index.php?view=speaker&id='.$row->id); ?>">
		<img class="speaker" src="<?php echo $this->row->pic; ?>" title="<?php echo $this->row->name; ?>" alt="<?php echo $this->row->name; ?>" />
	</a>
<?php }
if ($this->row->bio || ($this->row->intro && $this->params->get('speaker_intro'))) { ?>
	<h3 class="contentheading"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
	<?php
	echo $this->row->intro;
	echo $this->row->bio; ?>
	</p>
<?php }
if ($this->row->website && $this->row->website != "http://") { ?>
	<a href="<?php echo $this->row->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->row->name); ?></a>
<?php } ?>
<br style="clear:both" />
<!-- Begin Data - Sermons -->
<?php if($this->sermons) { ?>
<form method="post" id="adminForm" name="adminForm">
<table class="adminlist" cellpadding="2" cellspacing="2" width="100%">
<!-- Tabellenkopf mit Sortierlinks erstellen -->
	<thead>
		<tr>
			<?php if ($this->params->get('client_col_sermon_number')) { ?>
				<th width="5%" align="left"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php } ?>
			<th align="left"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONTITLE', 'sermon_title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php if ($this->params->get('client_col_sermon_scripture_reference')) { ?>
				<th align="left"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SCRIPTURE', 'sermon_scripture', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php } ?>
			<?php if ($this->params->get('client_col_sermon_date')) { ?>
				<th align="left">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONDATE', 'sermon_date', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
			<?php }
			if ($this->params->get('client_col_sermon_time')) { ?>
			<th align="center"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONTIME', 'sermon_time', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php }
			if ($this->params->get('client_col_sermon_series')) { ?>
			<th align="center"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERIES', 'series_title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php }
			if ($this->params->get('client_col_sermon_addfile')) { ?>
			<th align="left"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php } ?>
		</tr>
	</thead>
<!-- Begin Data -->
<?php
$i = 0;
	foreach($this->sermons as $sermon) {
		echo "<tr class=\"row$i\">";
		$i = 1 - $i;
		if( $this->params->get('client_col_sermon_number')){
			echo "<td align=\"left\" valign=\"middle\" > $sermon->sermon_number </td>";
		} ?>
		<td align="left">
			&nbsp;<a href="<?php echo $sermon->link1; ?>"><img title="<?php echo JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER'); ?>" src="<?php echo JURI::root().'components/com_sermonspeaker/images/play.gif'; ?>" class='icon_play' width='16' height='16' border='0' alt="" /></a>
			<a title="<?php echo JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER'); ?>" href="<?php echo $sermon->link2; ?>">
				<?php echo $sermon->sermon_title; ?>
			</a>
		</td>
		<?php
		if( $this->params->get('client_col_sermon_scripture_reference')){
			echo "<td align=\"left\" valign=\"middle\" >$sermon->sermon_scripture</td>";
		}
		if( $this->params->get('client_col_sermon_date')){
			echo "<td align=\"left\" valign=\"middle\">".JHTML::date($sermon->sermon_date, JText::_($this->params->get('date_format')), 0)."</td>";
		}
		if( $this->params->get('client_col_sermon_time')){
			echo "<td align=\"center\" valign=\"middle\">".SermonspeakerHelperSermonspeaker::insertTime($sermon->sermon_time)."</td>";
		}
		if ($this->params->get('client_col_sermon_series')) { ?>
			<td align="center"><a href="<?php echo JRoute::_("index.php?view=serie&id=$sermon->series_id"); ?>"><?php echo $sermon->series_title; ?></a></td>
		<?php }
		if ($this->params->get('client_col_sermon_addfile')) { ?>
			<td><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($sermon->addfile, $sermon->addfileDesc); ?></td>
		<?php }
		echo "</tr>";
	} // end of foreach ?>
</table>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<?php } else { ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
<?php } ?>
</div>