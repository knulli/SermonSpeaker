<?php
defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('bootstrap.tooltip');

$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrderSermons	= $this->state_sermons->get('list.ordering');
$listDirnSermons	= $this->state_sermons->get('list.direction');
$listOrderSeries	= $this->state_series->get('list.ordering');
$listDirnSeries		= $this->state_series->get('list.direction');
$limit 		= (int)$this->params->get('limit', '');
$player		= new SermonspeakerHelperPlayer($this->sermons);
$this->document->addScriptDeclaration('jQuery(function() {
		if (location.hash == \'#series\') {
			tab = \'#tab_series\';
		} else {
			tab = \'#tab_sermons\';
		}
		jQuery(\'#speakerTab a[href="\' + tab + \'"]\').tab(\'show\');
	})');
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-speaker-container<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<div class="<?php echo ($this->item->state) ? '': 'system-unpublished'; ?>">
		<div class="btn-group pull-right">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="icon-cog"></i>
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li class="email-icon"><?php echo JHtml::_('icon.email', $this->item, $this->params, array('type' => 'speaker')); ?></li>
				<?php if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
					<li class="edit-icon"><?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'speaker')); ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="page-header">
			<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug)); ?>">
				<h2><?php echo $this->item->name; ?></h2>
			</a>
			<?php if (!$this->item->state) : ?>
				<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
			<?php endif; ?>
		</div>
		<?php if ($this->item->pic) : ?>
			<div class="img-polaroid pull-right item-image item-image">
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug)); ?>">
					<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->item->pic); ?>">
				</a>
			</div>
		<?php endif; ?>
		<div class="article-info speaker-info muted">
			<dl class="article-info">
				<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
				<?php if (in_array('speaker:category', $this->columns) and $this->item->category_title) : ?>
					<dd>
						<div class="category-name">
							<i class="icon-folder"></i>
							<?php echo JText::_('JCATEGORY'); ?>:
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakersRoute($this->item->catslug)); ?>"><?php echo $this->item->category_title; ?></a>
						</div>
					</dd>
				<?php endif;
				if (in_array('speaker:hits', $this->columns)) : ?>
					<dd>
						<div class="hits">
							<i class="icon-eye-open"></i>
							<?php echo JText::_('JGLOBAL_HITS'); ?>:
							<?php echo $this->item->hits; ?>
						</div>
					</dd>
				<?php endif;
				if ($this->item->website) : ?>
					<dd>
						<div class="website">
							<i class=" icon-out-2"></i>
							<a href="<?php echo $this->item->website; ?>">
								<?php echo JText::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?>
							</a>
						</div>
					</dd>
				<?php endif; ?>
			</dl>
		</div>
		<?php if (in_array('speaker:intro', $this->columns) and $this->item->intro) : ?>
			<div>
				<?php echo JHTML::_('content.prepare', $this->item->intro, '', 'com_sermonspeaker.intro'); ?>
			</div>
		<?php endif;
		if(in_array('speaker:bio', $this->columns) and $this->item->bio) : ?>
			<div>
				<?php echo JHTML::_('content.prepare', $this->item->bio, '', 'com_sermonspeaker.bio'); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
	<ul class="nav nav-pills" id="speakerTab">
		<li><a href="#tab_sermons" data-toggle="pill"><?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?></a></li>
		<li><a href="#tab_series" data-toggle="pill"><?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?></a></li>
	</ul>
	<div class="pill-content">
		<div class="pill-pane" id="tab_sermons">
			<?php if (in_array('speaker:player', $this->col_sermon) and count($this->sermons)) :
				JHTML::stylesheet('com_sermonspeaker/player.css', '', true); ?>
				<div class="ss-sermons-player span10 offset1">
					<hr class="ss-sermons-player" />
					<?php if ($player->player != 'PixelOut') : ?>
						<div id="playing">
							<img id="playing-pic" class="picture" src="" />
							<span id="playing-duration" class="duration"></span>
							<div class="text">
								<span id="playing-title" class="title"></span>
								<span id="playing-desc" class="desc"></span>
							</div>
							<span id="playing-error" class="error"></span>
						</div>
					<?php endif;
					echo $player->mspace;
					echo $player->script;
					?>
					<hr class="ss-sermons-player" />
					<?php if ($player->toggle) : ?>
						<div class="span2 offset4 btn-group">
							<img class="btn" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
							<img class="btn" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="cat-items">
				<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString().'#sermons'); ?>" method="post" id="adminForm" name="adminForm" class="form-inline">
					<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
						<div class="filters btn-toolbar">
							<?php if ($this->params->get('filter_field')) :?>
								<div class="btn-group">
									<label class="filter-search-lbl element-invisible" for="filter-search">
										<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
										<?php echo JText::_('JGLOBAL_FILTER_LABEL').'&#160;'; ?>
									</label>
									<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state_sermons->get('filter.search')); ?>" class="input-medium" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" />
								</div>
								<div class="btn-group filter-select">
									<select name="book" id="filter_books" class="input-medium" onchange="this.form.submit()">
										<?php echo JHtml::_('select.options', $this->books, 'value', 'text', $this->state_sermons->get('scripture.book'), true);?>
									</select>
									<select name="month" id="filter_months" class="input-medium" onchange="this.form.submit()">
										<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_MONTH'); ?></option>
										
										<?php echo JHtml::_('select.options', $this->months, 'value', 'text', $this->state_sermons->get('date.month'), true);?>
									</select>
									<select name="year" id="filter_years" class="input-medium" onchange="this.form.submit()">
										<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_YEAR'); ?></option>
										<?php echo JHtml::_('select.options', $this->years, 'year', 'year', $this->state_sermons->get('date.year'), true);?>
									</select>
								</div>
							<?php endif;
							if ($this->params->get('show_pagination_limit')) : ?>
								<div class="btn-group pull-right">
									<label class="element-invisible">
										<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
									</label>
									<?php echo $this->pag_sermons->getLimitBox(); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="clearfix"></div>
					<?php if (!count($this->sermons)) : ?>
						<div class="no_entries alert alert-error"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
					<?php else : ?>
						<ul class="category list-striped list-condensed">
							<?php foreach($this->sermons as $i => $item) :
								$sep = 0; ?>
								<li id="sermon<?php echo $i; ?>" class="<?php echo ($item->state) ? '': 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
									<?php if (in_array('speaker:hits', $this->col_sermon)) : ?>
										<span class="ss-hits badge badge-info pull-right">
											<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
										</span>
									<?php endif;
									if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<span class="list-edit pull-left width-50">
											<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?>
										</span>
									<?php endif; ?>
									<strong class="ss-title">
										<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player); ?>
									</strong>
									<?php if (!$item->state) : ?>
										<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
									<?php endif; ?>
									<br />
									<?php if (in_array('speaker:series', $this->col_sermon) and $item->series_title) :
										if ($sep) : ?>
											|
										<?php endif;
										$sep = 1; ?>
										<small class="ss-series">
											<?php echo JText::_('COM_SERMONSPEAKER_SERIE'); ?>: 
											<?php if ($item->series_state): ?>
												<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>"><?php echo $item->series_title; ?></a>
											<?php else :
												echo $item->series_title;
											endif; ?>
										</small>
									<?php endif;
									if (in_array('speaker:length', $this->col_sermon) and $item->sermon_time != '00:00:00') :
										if ($sep) : ?>
											|
										<?php endif;
										$sep = 1; ?>
										<small class="ss-length">
											<?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>: 
											<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
										</small>
									<?php endif;
									if (in_array('speaker:scripture', $this->col_sermon) and $item->scripture) :
										if ($sep) : ?>
											|
										<?php endif;
										$sep = 1; ?>
										<small class="ss-scripture">
											<?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>: 
											<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
											echo JHTML::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
										</small>
									<?php endif;
									if (in_array('speaker:date', $this->col_sermon) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<span class="ss-date small pull-right">
											<?php echo JHTML::date($item->sermon_date, JText::_($this->params->get('date_format')), true); ?>
										</span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif;
					if ($user->authorise('core.create', 'com_sermonspeaker')) :
						echo JHtml::_('icon.create', $this->category, $this->params);
					endif;
					if ($this->params->get('show_pagination') and ($this->pag_sermons->get('pages.total') > 1)) : ?>
						<div class="pagination">
							<?php if ($this->params->get('show_pagination_results', 1)) : ?>
								<p class="counter pull-right">
									<?php echo $this->pag_sermons->getPagesCounter(); ?>
								</p>
							<?php endif;
							echo $this->pag_sermons->getPagesLinks(); ?>
						</div>
					<?php endif; ?>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
					<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
					<input type="hidden" name="limitstart" value="" />
				</form>
			</div>
		</div>
		<div class="pill-pane" id="tab_series">
			<div class="cat-items">
				<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString().'#series'); ?>" method="post" id="adminFormSeries" name="adminFormSeries">
					<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
						<div class="filters btn-toolbar">
							<?php if ($this->params->get('show_pagination_limit')) : ?>
								<div class="btn-group pull-right">
									<label class="element-invisible">
										<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
									</label>
									<?php echo $this->pag_series->getLimitBox(); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="clearfix"></div>
					<?php if (!count($this->series)) : ?>
						<div class="no_entries alert alert-error"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERIES')); ?></div>
					<?php else : ?>
						<ul class="category list-striped list-condensed">
							<?php foreach($this->series as $i => $item) :
								$sep = 0; ?>
								<li class="<?php echo ($item->state) ? '': 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
									<?php if (in_array('speaker:hits', $this->col_serie)) : ?>
										<span class="ss-hits badge badge-info pull-right">
											<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
										</span>
									<?php endif;
									if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<span class="list-edit pull-left width-50">
											<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'serie')); ?>
										</span>
									<?php endif; ?>
									<strong class="ss-title">
										<a title="<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>">
											<?php echo $item->series_title; ?>
										</a>
									</strong>
									<?php if (!$item->state) : ?>
										<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif;
					if ($this->params->get('show_pagination') and ($this->pag_series->get('pages.total') > 1)) : ?>
						<div class="pagination">
							<?php if ($this->params->get('show_pagination_results', 1)) : ?>
								<p class="counter pull-right">
									<?php echo $this->pag_series->getPagesCounter(); ?>
								</p>
							<?php endif;
							echo $this->pag_series->getPagesLinks(); ?>
						</div>
					<?php endif; ?>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
					<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
					<input type="hidden" name="limitstart" value="" />
				</form>
			</div>
		</div>
	</div>
</div>