</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    window.GI = { base: "<?= BASEURL ?>", auth: true };
    <?php if ($flash): ?>window.GI_FLASH = { type: "<?= e($flash['type']) ?>", message: "<?= e($flash['message']) ?>" };<?php endif; ?>
</script>
<script src="<?= BASEURL ?>public/assets/js/app.js?v=3"></script>
<?php if (!empty($pageScript)) echo $pageScript; ?>
</body>
</html>
