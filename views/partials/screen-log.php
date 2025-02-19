<?php
// $items

// pending, error, sent
use dcms\enqueu\includes\Database;
use dcms\enqueu\helpers\StateName;

$db = new Database();

$items = [];
$status = '';
$fields_header = [];
$count = 0;

$current_status = isset( $_GET['status'] ) ? $_GET['status'] : StateName::pending;
$url_status = admin_url( DCMS_ENQUEU_PAGE_ENQUEUE . "&page=enqueu-email&tab=cron-log&status=");

switch ($current_status) {
    case StateName::pending:
            $items = $db->get_pending_emails();
            $status = '<span class="state pending">Pendiente</span>';
            $fields_header = ['ID', 'Correo', 'Asunto', 'Estado', 'Registro'];
            break;
    case StateName::error:
            $items = $db->get_error_emails();
            $status = '<span class="state error">Error</span>';
            $fields_header = ['ID', 'Correo', 'Asunto', 'Estado', 'Registro', 'Enviado'];
            break;
    case StateName::sent:
            $items = $db->get_sent_emails();
            $status = '<span class="state sent">Enviado</span>';
            $fields_header = ['ID', 'Correo', 'Asunto', 'Estado', 'Registro', 'Enviado'];
            break;
}
?>

<section class="container-report" >

<ul class="subsubsub ">
	<li><a href="<?= $url_status . StateName::pending ?>" class="<?= $current_status == StateName::pending ?'current':'' ?>">Pendientes</a> |</li>
	<li><a href="<?= $url_status . StateName::error ?>" class="<?= $current_status == StateName::error ?'current':'' ?>">Erroneos</a> |</li>
	<li><a href="<?= $url_status . StateName::sent ?>" class="<?= $current_status == StateName::sent ?'current':'' ?>">Enviados</a></li>
</ul>

<section class="table-container">
    <table class="dcms-table">
        <tr>
            <?php
            foreach($fields_header as $field) {
                echo "<th>" . $field . "</th>";
            }
            ?>
        </tr>
        <?php foreach ($items as $item):  ?>
            <?php
                $atts = json_decode(base64_decode($item->data), true);
                $email = $atts['to'];
                $subject = $atts['subject'];
                $count++;
            ?>
            <tr>
                <td><?= $item->id ?></td>
                <td><?= $email ?></td>
                <td><?= $subject ?></td>
                <td><?= $status ?></td>
                <td><?= $item->created ?></td>
                <?php if ( $current_status != StateName::pending ): ?>
                    <td><?= $item->updated ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="header">
        <strong>Los <?= $count ?> recientes</strong>

        <?php if ( $current_status == StateName::pending ): ?>
        <section class="buttons">
            <form method="post" id="frm-force" class="frm-force" action="<?php echo admin_url( 'admin-post.php' ) ?>" >
                <input type="hidden" name="action" value="process_force_sent">
                <button type="submit" class="btn-force button button-primary"><?php _e('Forzar envío', 'dcms-enqueu-email') ?></button>
            </form>
        </section>
        <?php endif; ?>
    </div>
</section>


