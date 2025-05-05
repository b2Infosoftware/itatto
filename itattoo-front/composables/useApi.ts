export const useApi = async (
  method: 'GET' | 'POST' | 'PATCH' | 'DELETE',
  urlPath: string,
  payload: any = null
) => {
  const config = useRuntimeConfig();
  const bearerToken = 'Bearer ' + useCookie('auth_token').value;

  /**
   * Handling GET Requests
   */
  if (method == 'GET') {
    // $fetch sends arrays in a format unsupported by laravel by default
    // so we're making a little tweak in here
    const queryData = { ...payload };
    for (const key in queryData) {
      const element = queryData[key];
      if (Array.isArray(element) && !key.includes('[]')) {
        const newKey = key + '[]';
        queryData[newKey] = element;
        delete queryData[key];
      }
    }
    try {
      const response = await $fetch(urlPath, {
        baseURL: config.public.API_BASE_URL,
        params: queryData,
        headers: {
          Authorization: bearerToken,
          accept: 'application/json',
        },
      });

      return response;
    } catch (error: any) {
      if (error.statusCode == 401) {
        useCookie('auth_token').value = null;
        return navigateTo({ name: 'login' });
      }
      if (error.statusCode == 403) {
        useSnackbarStore().show(error.data.message, 'error');
        return navigateTo({ name: 'index' });
      }
      throw new Error(error.data.message);
    }
  }

  /**
   * Handling POST / PATCH / DELETE Requests
   */
  try {
    // Make sure we clear any existing API validation errors from the store first
    useValidationStore().clearErrors();

    // make the call
    const response = await $fetch(urlPath, {
      baseURL: config.public.API_BASE_URL,
      method: method,
      body: payload,
      headers: {
        Authorization: bearerToken,
        accept: 'application/json',
      },
    });

    return response;
  } catch (error: any) {
    useSnackbarStore().show(error.data.message, 'error');
    // Abort all and redirect to login if user token expired
    if (error.statusCode == 401) {
      if (useRoute().name != 'login') {
        return navigateTo({ name: 'login' });
      }
    }
    // Populate the validation store with any errors
    // if there's any issue with the fields/requests on the API
    if (error.statusCode === 422) {
      useValidationStore().populateErrors(error.data.errors);
    } else {
      // populate store with general message AND maybe throw a nice notify
      useValidationStore().apiOverallMessage = error.data.message;
    }
    if (![404, 422].includes(error.statusCode)) {
      throw new Error(error.data.message);
    }
  }
};
