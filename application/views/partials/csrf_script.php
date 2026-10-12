<?php if ($this->config->item('csrf_protection')): ?>
<script>
window.esbaCsrf = <?= json_encode([
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash(),
    'cookie' => $this->config->item('cookie_prefix') . $this->config->item('csrf_cookie_name'),
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="<?= base_url('assets/js/csrf.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/csrf.js') ?>"></script>
<?php endif; ?>
