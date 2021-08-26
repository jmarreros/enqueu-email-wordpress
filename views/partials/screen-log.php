<?php
// $items
?>

<section class="container-report" >

<?php
    $fields = ['ID', 'Correo', 'Asunto', 'Estado', 'Fecha'];
    $count = 0;
?>

<section class="table-container">
    <table class="dcms-table">
        <tr>
            <?php
            foreach($fields as $field) {
                echo "<th>" . $field . "</th>";
            }
            ?>
        </tr>
    <?php foreach ($items as $item):  ?>
        <?php
        $atts = json_decode(base64_decode($item->data), true);

        $email = $atts['to'];
        $subject = $atts['subject'];
        $status  = $item->status == 0
                    ? '<span class="state pending">Pendiente</span>'
                    : '<span class="state pending">Enviado</span>';
        $count++;
        ?>
        <tr>
            <td><?= $item->id ?></td>
            <td><?= $email ?></td>
            <td><?= $subject ?></td>
            <td><?= $status ?></td>
            <td><?= $item->date ?></td>
        </tr>
    <?php endforeach; ?>
    </table>
    <div class="header">
        <strong>Total pendientes: <?= $count ?></strong>

        <section class="buttons">
            <form method="post" id="frm-force" class="frm-force" action="<?php echo admin_url( 'admin-post.php' ) ?>" >
                <input type="hidden" name="action" value="process_force_sent">
                <button type="submit" class="btn-force button button-primary"><?php _e('Forzar envío', 'dcms-enqueu-email') ?></button>
            </form>
        </section>
    </div>
</section>


