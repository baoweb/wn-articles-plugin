<div class="form-control attachment-links">
    <table style="width: 100%;">
    <?php foreach($model->attachments as $attachment): ?>
        <tr>
            <td style="width: 40px;"><i class="icon-file"></i></td>
            <td>
                <?= $attachment->file_name ?>
            </td>
            <td align="right">
                <a href="<?= '/attachments/get/' . $attachment->id ?>"><?php echo '/attachments/get/' . $attachment->id ?></a>

                <span style="margin-left: 1.5em;">
                    <a class="btn btn-secondary btn-xs" style="padding: 5px 5px 5px 15px;" onClick="navigator.clipboard.writeText('<?= '/attachments/get/' . $attachment->id ?>');"><i class="wn-icon-copy" style="margin: 0; padding: 0; width: 1em; text-align: center"></i></a>
                </span>
            </td>

        </tr>
    <?php endforeach; ?>
    </table>
</div>