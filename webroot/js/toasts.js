function initializeToasts() {
  $('#flash .toast').each(function (index, element) {

    // Toasts that do have an instance have already been fired. Don't reinitialize them !
    if (!bootstrap.Toast.getInstance(element)) {
      var toast = new bootstrap.Toast(element);
      toast.show();
    }

  });
}

initializeToasts();
