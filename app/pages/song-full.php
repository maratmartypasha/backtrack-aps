<?php 

	db_query("update songs set views = views + 1 where id = :id limit 1",['id'=>$row['id']]);
?>

<!--start music card-->
<div class="music-card-full" style="max-width: 800px;">
	
	<h2 class="card-title"><?=esc($row['title'])?></h2>
	<div class="card-subtitle">by: <?=esc(get_artist($row['artist_id']))?></div>

	<div style="overflow: hidden;">
		<a href="<?=ROOT?>/song/<?=$row['slug']?>"><img src="<?=ROOT?>/<?=$row['image']?>"></a>
	</div>
	<div class="card-content">
		<audio controls style="width: 100%">
			<source src="<?=ROOT?>/<?=$row['file']?>" type="audio/mpeg">
		</audio>

		<div>Views: <?=$row['views']?></div>
		<div>Date added: <?=get_date($row['date'])?></div>

		<a href="<?=ROOT?>/download/<?=$row['slug']?>">
			<button class="btn bg-purple">Download</button>
		</a>
	</div>
</div>
<!--end music card-->