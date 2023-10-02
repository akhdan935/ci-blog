<?php
foreach($record as $key => $value){
?>
<div class="post-preview">
    <a href="<?= set_post_link($value['post_id']) ?>">
        <h2 class="post-title"><?= $value['post_title'] ?></h2>
        <?php if($value['post_description']){ ?>
        <h3 class="post-subtitle"><?= $value['post_description'] ?></h3>
        <?php } ?>
    </a>
    <p class="post-meta">
        Posted by
        <a href="#!"><?= post_penulis($value['username']) ?></a>
        on <?= tanggal_indonesia($value['post_time']) ?>
    </p>
</div>
<!-- Divider-->
<hr class="my-4" />
<?php } ?>
<?= $pager->simpleLinks('ft', 'depan') ?>