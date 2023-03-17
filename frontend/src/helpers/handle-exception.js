export default {
  handleApiException(appContext, error) {
    const statusCode = error?.response?.status;
    const statusText = error?.response?.statusText;

    switch (statusCode) {
      case 422:
        this.handleValidationError(appContext, error?.response?.data?.errors);
        break;
      case 404:
        // appContext.$toastr.e(appContext.$tr('record_not_found'));
        appContext.$toasterNA("red", appContext.$tr('record_not_found'));

        break;
      default:
        // appContext.$toastr.e(appContext.$tr('something_went_wrong'));
        appContext.$toasterNA("red", appContext.$tr('something_went_wrong'));

        break;
    }
  },

  handleValidationError(appContext, errors) {
    if (errors instanceof Object || typeof errors === 'object') {
      for (const errorKey of Object.keys(errors)) {
        if (errors[errorKey] instanceof Array) {
          for (const errorText of errors[errorKey]) {
            // appContext.$toastr.e(errorText);
        appContext.$toasterNA("red", errorText);

          }
        } else {
          // appContext.$toastr.e(errors[errorKey]);
        appContext.$toasterNA("red", errors[errorKey]);

        }
      }
    } else {
      // appContext.$toastr.e(appContext.$tr('something_went_wrong'));
      appContext.$toasterNA("red", appContext.$tr('something_went_wrong'));

    }
  },
};
