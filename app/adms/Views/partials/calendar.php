<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#myDate", {
      dateFormat: "d/m/Y",
      maxDate: "today",
      mode: "range",
      theme: "default",
      locale: "pt"
    });
  });
</script>