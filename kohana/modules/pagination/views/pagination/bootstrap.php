<div class="pagination">
  <ul>   
    <?php if ($previous_page !== FALSE): ?>
		<li><a href="<?php echo $page->url($previous_page) ?>">Prev</a></li>
	<?php else: ?>
		<li class="disabled"><span>Prev</span></li>
	<?php endif ?>
	
	<?php for ($i = 1; $i <= $total_pages; $i++): ?>

		<?php if ($i == $current_page): ?>
		    <li class="active"><a href="<?php echo $page->url($i) ?>"><?php echo $i ?></a></li>
		<?php else: ?>
			<li><a href="<?php echo $page->url($i) ?>"><?php echo $i ?></a></li>
		<?php endif ?>

	<?php endfor ?>
	
	<?php if ($next_page !== FALSE): ?>
		<li><a href="<?php echo $page->url($next_page) ?>">Next</a></li>
	<?php else: ?>
		<li class="disabled"><span>Next</span></li>
	<?php endif ?>
    
  </ul>
</div>