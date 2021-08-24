<h2>Log</h2>


<section class="buttons">
    <form method="post" id="frm-force" class="frm-force" action="<?php echo admin_url( 'admin-post.php' ) ?>" >
        <input type="hidden" name="action" value="process_force_sent">
        <button type="submit" class="btn-force button button-primary"><?php _e('Forzar envío', 'dcms-enqueu-email') ?></button>
    </form>
</section>
