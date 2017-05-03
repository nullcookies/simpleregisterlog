<?php if ($filters) : ?>
<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-body">
			<?php echo $filters->render('filters') ?>
		</div>
	</div>
</div>
<?php endif; ?>

<div class="container-fluid">
	<div class="panel panel-default">
		<table class="table table-bordered table-hover panel-body">
			<thead>
				<tr>
					<?php if ($batch): ?>
					<th class="batch_action"><input type="checkbox" class="batch_select_all"></th>
					<?php endif; ?>
					<?php foreach($columns as $column => $column_conf): ?>
					<th class="column_<?php echo $column ?>"><?php echo $column_conf['options']['sort_link']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody<?php if (isset($rowlink)) echo ' data-link="row" class="rowlink"'; ?>>
				<?php foreach($content as $idx => $row) : ?>
				<tr class="<?php echo $row->getRowStatusClass(); ?>" title="<?php echo $row->getRowTitle(); ?>" row-id="<?php echo htmlspecialchars($row->getRowId()); ?>">
					<?php if ($batch): ?>
					<td class="batch_action rowlink-skip"><input type="checkbox" class="batch_select_row"></td>
					<?php endif; ?>
					<?php foreach($columns as $column => $column_conf): ?>
					<td class="field_<?php echo $column ?> field_class_<?php echo $column_conf['type'] ?>"><?php
						echo $column_conf['options']['value_formatter']($column, $row);
					?></td>
					<?php endforeach; ?>
				</tr>
				<?php endforeach; ?>
			<tbody>

			<?php if (isset($total)) : ?>
			<tfoot>
				<tr>
					<?php $i = 0; foreach($columns as $column => $column_conf): $i++ ?>
						<?php if ($i == 1) : ?>
							<td class="field_total_name field_class_string"<?php if ($batch) echo ' colspan="2";'?>><?php echo $total_name ?></td>
						<?php elseif (isset($totals_field[$column])): ?>
							<td class="field_<?php echo $column ?> field_class_<?php echo $column_conf['type'] ?>"><?php
								echo $column_conf['options']['value_formatter']($column, $total);
							?></td>
						<?php else: ?>
							<td>-</td>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			</tfoot>
			<?php endif; ?>

		</table>
		<?php if (isset($rowlink)) echo $rowlink; ?>
	</div>
</div>

<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-body">
			<?php if (isset($batch)) echo $batch->render(); ?>
		</div>
	</div>
</div>

<?php if (isset($pager)) echo $pager; ?>
