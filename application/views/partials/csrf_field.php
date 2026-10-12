<?php if ($this->config->item('csrf_protection')): ?>
<input type="hidden" name="<?= html_escape($this->security->get_csrf_token_name()) ?>" value="<?= html_escape($this->security->get_csrf_hash()) ?>">
<?php endif; ?>
