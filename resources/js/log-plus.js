document.addEventListener("DOMContentLoaded", function () {
  // Code to be executed when DOM is ready
  var log_file_select = document.getElementById("log-file-select");

  // Attach change event handler
  log_file_select.addEventListener("change", function () {
    // Trigger form submission
    document.getElementById("log-select-form").submit();
  });
});

function showFullMessage(id)
{
  document.getElementById('full-message-'+id).classList.toggle('hidden');
}
