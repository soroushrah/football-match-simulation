import {useToast} from "vue-toastification";

export const handelApiResponseError = (response) => {
  const toast = useToast();
  if (response) {
    const errors = response.data.errors;
    const size = Object.keys(errors).length;

    if (size > 0) {
      for (const property in errors) {
        toast.error(`${errors[property]}`);
      }
    } else {
      if (response.data.message) {
        toast.error(response.data.message);
      } else {
        toast.error(response.message);
      }
    }
  }
};

export const getTextInitials = (name) => {
  return name
      .split(' ')
      .map(word => word.charAt(0))
      .join('')
      .substring(0, 2);
};